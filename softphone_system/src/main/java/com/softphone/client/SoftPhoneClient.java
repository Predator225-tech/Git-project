package com.softphone.client;

import com.google.gson.Gson;
import com.google.gson.JsonObject;
import java.io.*;
import java.net.*;
import java.util.concurrent.*;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

/**
 * Client SoftPhone - Gère la communication TCP et UDP
 */
public class SoftPhoneClient {
    private static final Logger logger = LoggerFactory.getLogger(SoftPhoneClient.class);
    
    private String username;
    private String serverAddress;
    private int serverPort = 5060;
    private int localUdpPort;
    private Socket tcpSocket;
    private PrintWriter tcpOut;
    private BufferedReader tcpIn;
    private DatagramSocket udpSocket;
    private Gson gson;
    private String currentCallId;
    private AudioHandler audioHandler;
    private ExecutorService executorService;
    private boolean connected = false;

    public SoftPhoneClient(String username, String serverAddress, int localUdpPort) {
        this.username = username;
        this.serverAddress = serverAddress;
        this.localUdpPort = localUdpPort;
        this.gson = new Gson();
        this.executorService = Executors.newFixedThreadPool(3);
    }

    /**
     * Établit une connexion avec le serveur
     */
    public boolean connect() {
        try {
            tcpSocket = new Socket(serverAddress, serverPort);
            tcpOut = new PrintWriter(tcpSocket.getOutputStream(), true);
            tcpIn = new BufferedReader(new InputStreamReader(tcpSocket.getInputStream()));
            
            udpSocket = new DatagramSocket(localUdpPort);
            udpSocket.setReceiveBufferSize(64000);
            
            audioHandler = new AudioHandler(udpSocket);
            
            // Enregistrer l'utilisateur
            register();
            
            // Écouter les messages du serveur
            executorService.execute(this::listenTcpMessages);
            executorService.execute(audioHandler::startReceiving);
            
            connected = true;
            logger.info("Connecté au serveur: {}:{}", serverAddress, serverPort);
            return true;
        } catch (IOException e) {
            logger.error("Erreur de connexion au serveur", e);
            return false;
        }
    }

    /**
     * Enregistre l'utilisateur auprès du serveur
     */
    private void register() {
        JsonObject registerMsg = new JsonObject();
        registerMsg.addProperty("action", "REGISTER");
        registerMsg.addProperty("username", username);
        registerMsg.addProperty("ip", getLocalIpAddress());
        registerMsg.addProperty("udpPort", localUdpPort);
        
        sendTcpMessage(registerMsg);
    }

    /**
     * Récupère l'adresse IP locale
     */
    private String getLocalIpAddress() {
        try {
            return InetAddress.getLocalHost().getHostAddress();
        } catch (UnknownHostException e) {
            return "127.0.0.1";
        }
    }

    /**
     * Initie un appel à un utilisateur
     */
    public void initiateCall(String targetUsername) {
        JsonObject callMsg = new JsonObject();
        callMsg.addProperty("action", "CALL");
        callMsg.addProperty("targetUsername", targetUsername);
        
        sendTcpMessage(callMsg);
        logger.info("Appel initié vers: {}", targetUsername);
    }

    /**
     * Répond à un appel entrant
     */
    public void answerCall(String callId, String callerIp, int callerUdpPort) {
        currentCallId = callId;
        
        JsonObject answerMsg = new JsonObject();
        answerMsg.addProperty("action", "ANSWER");
        answerMsg.addProperty("callId", callId);
        
        sendTcpMessage(answerMsg);
        
        // Commencer à envoyer de l'audio
        audioHandler.setRemoteAddress(callerIp, callerUdpPort);
        audioHandler.startSending();
        
        logger.info("Appel accepté: {}", callId);
    }

    /**
     * Termine l'appel en cours
     */
    public void hangupCall() {
        if (currentCallId != null) {
            JsonObject hangupMsg = new JsonObject();
            hangupMsg.addProperty("action", "HANGUP");
            hangupMsg.addProperty("callId", currentCallId);
            
            sendTcpMessage(hangupMsg);
            audioHandler.stopSending();
            currentCallId = null;
            
            logger.info("Appel terminé");
        }
    }

    /**
     * Récupère la liste des utilisateurs enregistrés
     */
    public void listUsers() {
        JsonObject listMsg = new JsonObject();
        listMsg.addProperty("action", "LIST_USERS");
        
        sendTcpMessage(listMsg);
    }

    /**
     * Écoute les messages du serveur TCP
     */
    private void listenTcpMessages() {
        try {
            String message;
            while (connected && (message = tcpIn.readLine()) != null) {
                handleServerMessage(message);
            }
        } catch (IOException e) {
            logger.error("Erreur lors de la réception des messages", e);
        }
    }

    /**
     * Traite les messages reçus du serveur
     */
    private void handleServerMessage(String message) {
        try {
            JsonObject jsonMsg = gson.fromJson(message, JsonObject.class);
            String action = jsonMsg.get("action").getAsString();

            switch (action) {
                case "REGISTER_SUCCESS":
                    logger.info("Enregistrement réussi");
                    break;
                case "INCOMING_CALL":
                    handleIncomingCall(jsonMsg);
                    break;
                case "CALL_ANSWERED":
                    logger.info("Appel accepté par le destinataire");
                    break;
                case "CALL_TERMINATED":
                    logger.info("Appel terminé");
                    break;
                case "USER_LIST":
                    logger.info("Utilisateurs en ligne: {}", jsonMsg.get("users"));
                    break;
                case "ERROR":
                    logger.error("Erreur du serveur: {}", jsonMsg.get("message"));
                    break;
            }
        } catch (Exception e) {
            logger.error("Erreur lors du traitement du message", e);
        }
    }

    /**
     * Traite un appel entrant
     */
    private void handleIncomingCall(JsonObject jsonMsg) {
        String callId = jsonMsg.get("callId").getAsString();
        String caller = jsonMsg.get("caller").getAsString();
        String callerIp = jsonMsg.get("callerIp").getAsString();
        int callerUdpPort = jsonMsg.get("callerUdpPort").getAsInt();

        logger.info("Appel entrant de: {} ({}:{})", caller, callerIp, callerUdpPort);
        // À implémenter dans la GUI pour demander à l'utilisateur de répondre
    }

    /**
     * Envoie un message TCP
     */
    private void sendTcpMessage(JsonObject message) {
        if (tcpOut != null) {
            tcpOut.println(message.toString());
        }
    }

    /**
     * Ferme la connexion
     */
    public void disconnect() {
        connected = false;
        try {
            if (tcpSocket != null) tcpSocket.close();
            if (udpSocket != null) udpSocket.close();
            executorService.shutdown();
            logger.info("Déconnecté du serveur");
        } catch (IOException e) {
            logger.error("Erreur lors de la déconnexion", e);
        }
    }

    public boolean isConnected() {
        return connected;
    }

    public String getUsername() {
        return username;
    }
}

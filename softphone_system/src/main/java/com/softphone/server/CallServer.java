package com.softphone.server;

import com.google.gson.Gson;
import com.google.gson.JsonObject;
import java.io.*;
import java.net.*;
import java.util.*;
import java.util.concurrent.*;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

/**
 * Serveur de contrôle TCP pour la gestion des appels
 * Gère l'enregistrement des utilisateurs et l'établissement des appels
 */
public class CallServer {
    private static final Logger logger = LoggerFactory.getLogger(CallServer.class);
    private static final int TCP_PORT = 5060;
    
    private ServerSocket serverSocket;
    private ExecutorService threadPool;
    private Map<String, UserInfo> registeredUsers; // username -> UserInfo
    private Map<String, CallSession> activeCalls; // callId -> CallSession
    private Gson gson;

    public CallServer() {
        this.threadPool = Executors.newFixedThreadPool(10);
        this.registeredUsers = new ConcurrentHashMap<>();
        this.activeCalls = new ConcurrentHashMap<>();
        this.gson = new Gson();
    }

    /**
     * Démarre le serveur TCP
     */
    public void start() {
        try {
            serverSocket = new ServerSocket(TCP_PORT);
            logger.info("CallServer démarré sur le port TCP {}", TCP_PORT);
            
            while (true) {
                Socket clientSocket = serverSocket.accept();
                threadPool.execute(new ClientHandler(clientSocket));
            }
        } catch (IOException e) {
            logger.error("Erreur lors du démarrage du serveur", e);
        }
    }

    /**
     * Gestionnaire de connexion client
     */
    private class ClientHandler implements Runnable {
        private Socket socket;
        private PrintWriter out;
        private BufferedReader in;
        private String currentUsername;

        public ClientHandler(Socket socket) {
            this.socket = socket;
        }

        @Override
        public void run() {
            try {
                out = new PrintWriter(socket.getOutputStream(), true);
                in = new BufferedReader(new InputStreamReader(socket.getInputStream()));

                String message;
                while ((message = in.readLine()) != null) {
                    handleMessage(message);
                }
            } catch (IOException e) {
                logger.error("Erreur de communication avec le client", e);
            } finally {
                unregisterUser();
                closeConnection();
            }
        }

        /**
         * Traite les messages reçus du client
         */
        private void handleMessage(String message) {
            try {
                JsonObject jsonMsg = gson.fromJson(message, JsonObject.class);
                String action = jsonMsg.get("action").getAsString();

                switch (action) {
                    case "REGISTER":
                        handleRegister(jsonMsg);
                        break;
                    case "CALL":
                        handleCall(jsonMsg);
                        break;
                    case "ANSWER":
                        handleAnswer(jsonMsg);
                        break;
                    case "HANGUP":
                        handleHangup(jsonMsg);
                        break;
                    case "LIST_USERS":
                        handleListUsers();
                        break;
                    default:
                        logger.warn("Action inconnue: {}", action);
                }
            } catch (Exception e) {
                logger.error("Erreur lors du traitement du message", e);
            }
        }

        /**
         * Enregistre un utilisateur
         */
        private void handleRegister(JsonObject jsonMsg) {
            String username = jsonMsg.get("username").getAsString();
            String ipAddress = jsonMsg.get("ip").getAsString();
            int udpPort = jsonMsg.get("udpPort").getAsInt();

            currentUsername = username;
            UserInfo userInfo = new UserInfo(username, ipAddress, udpPort);
            registeredUsers.put(username, userInfo);

            JsonObject response = new JsonObject();
            response.addProperty("action", "REGISTER_SUCCESS");
            response.addProperty("message", "Utilisateur " + username + " enregistré");
            out.println(response.toString());

            logger.info("Utilisateur enregistré: {} ({}:{})", username, ipAddress, udpPort);
        }

        /**
         * Initie un appel
         */
        private void handleCall(JsonObject jsonMsg) {
            String targetUsername = jsonMsg.get("targetUsername").getAsString();

            if (!registeredUsers.containsKey(targetUsername)) {
                sendError("Utilisateur " + targetUsername + " non disponible");
                return;
            }

            String callId = UUID.randomUUID().toString();
            UserInfo caller = registeredUsers.get(currentUsername);
            UserInfo callee = registeredUsers.get(targetUsername);

            CallSession callSession = new CallSession(callId, caller, callee);
            activeCalls.put(callId, callSession);

            // Envoyer une notification d'appel au destinataire
            // Vous devrez implémenter une méthode pour notifier l'appelé
            JsonObject notification = new JsonObject();
            notification.addProperty("action", "INCOMING_CALL");
            notification.addProperty("callId", callId);
            notification.addProperty("caller", currentUsername);
            notification.addProperty("callerIp", caller.getIpAddress());
            notification.addProperty("callerUdpPort", caller.getUdpPort());

            logger.info("Appel initié: {} -> {} (callId: {})", currentUsername, targetUsername, callId);
        }

        /**
         * Répond à un appel
         */
        private void handleAnswer(JsonObject jsonMsg) {
            String callId = jsonMsg.get("callId").getAsString();

            CallSession session = activeCalls.get(callId);
            if (session != null) {
                session.setStatus("ACTIVE");
                JsonObject response = new JsonObject();
                response.addProperty("action", "CALL_ANSWERED");
                response.addProperty("callId", callId);
                out.println(response.toString());
                logger.info("Appel accepté: {}", callId);
            } else {
                sendError("Session d'appel non trouvée");
            }
        }

        /**
         * Termine un appel
         */
        private void handleHangup(JsonObject jsonMsg) {
            String callId = jsonMsg.get("callId").getAsString();

            CallSession session = activeCalls.remove(callId);
            if (session != null) {
                JsonObject response = new JsonObject();
                response.addProperty("action", "CALL_TERMINATED");
                response.addProperty("callId", callId);
                out.println(response.toString());
                logger.info("Appel terminé: {}", callId);
            }
        }

        /**
         * Liste tous les utilisateurs enregistrés
         */
        private void handleListUsers() {
            JsonObject response = new JsonObject();
            response.addProperty("action", "USER_LIST");
            response.add("users", gson.toJsonTree(registeredUsers.keySet()));
            out.println(response.toString());
        }

        /**
         * Envoie un message d'erreur
         */
        private void sendError(String message) {
            JsonObject error = new JsonObject();
            error.addProperty("action", "ERROR");
            error.addProperty("message", message);
            out.println(error.toString());
        }

        /**
         * Désenregistre l'utilisateur à la déconnexion
         */
        private void unregisterUser() {
            if (currentUsername != null) {
                registeredUsers.remove(currentUsername);
                logger.info("Utilisateur désenregistré: {}", currentUsername);
            }
        }

        /**
         * Ferme la connexion
         */
        private void closeConnection() {
            try {
                if (socket != null) socket.close();
                if (in != null) in.close();
                if (out != null) out.close();
            } catch (IOException e) {
                logger.error("Erreur lors de la fermeture de la connexion", e);
            }
        }
    }

    public static void main(String[] args) {
        CallServer server = new CallServer();
        server.start();
    }
}

/**
 * Informations d'un utilisateur enregistré
 */
class UserInfo {
    private String username;
    private String ipAddress;
    private int udpPort;

    public UserInfo(String username, String ipAddress, int udpPort) {
        this.username = username;
        this.ipAddress = ipAddress;
        this.udpPort = udpPort;
    }

    public String getUsername() { return username; }
    public String getIpAddress() { return ipAddress; }
    public int getUdpPort() { return udpPort; }
}

/**
 * Session d'appel active
 */
class CallSession {
    private String callId;
    private UserInfo caller;
    private UserInfo callee;
    private String status; // INITIATED, RINGING, ACTIVE, ENDED
    private long startTime;

    public CallSession(String callId, UserInfo caller, UserInfo callee) {
        this.callId = callId;
        this.caller = caller;
        this.callee = callee;
        this.status = "INITIATED";
        this.startTime = System.currentTimeMillis();
    }

    public String getCallId() { return callId; }
    public UserInfo getCaller() { return caller; }
    public UserInfo getCallee() { return callee; }
    public String getStatus() { return status; }
    public void setStatus(String status) { this.status = status; }
    public long getStartTime() { return startTime; }
}

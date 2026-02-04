package com.softphone.client;

import java.net.*;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

/**
 * Gestionnaire audio UDP pour les appels
 * Simule la transmission d'audio via UDP
 */
public class AudioHandler {
    private static final Logger logger = LoggerFactory.getLogger(AudioHandler.class);
    private static final int CHUNK_SIZE = 4096;
    private static final int SAMPLE_RATE = 8000;
    
    private DatagramSocket udpSocket;
    private String remoteAddress;
    private int remotePort;
    private boolean sending = false;
    private boolean receiving = true;
    private byte[] audioBuffer;

    public AudioHandler(DatagramSocket udpSocket) {
        this.udpSocket = udpSocket;
        this.audioBuffer = new byte[CHUNK_SIZE];
    }

    /**
     * Définit l'adresse de destination pour l'audio
     */
    public void setRemoteAddress(String address, int port) {
        this.remoteAddress = address;
        this.remotePort = port;
    }

    /**
     * Commence à envoyer de l'audio
     */
    public void startSending() {
        sending = true;
        logger.info("Transmission audio commencée vers {}:{}", remoteAddress, remotePort);
        
        // Simuler l'envoi d'audio
        new Thread(() -> {
            try {
                while (sending) {
                    // Générer des données audio simulées (bruit blanc)
                    generateAudioData();
                    
                    InetAddress destAddress = InetAddress.getByName(remoteAddress);
                    DatagramPacket packet = new DatagramPacket(
                        audioBuffer, 
                        audioBuffer.length, 
                        destAddress, 
                        remotePort
                    );
                    
                    udpSocket.send(packet);
                    
                    // Délai pour simuler le débit audio
                    Thread.sleep(20); // 20ms par chunk
                }
            } catch (Exception e) {
                logger.error("Erreur lors de l'envoi audio", e);
            }
        }).start();
    }

    /**
     * Arrête l'envoi d'audio
     */
    public void stopSending() {
        sending = false;
        logger.info("Transmission audio arrêtée");
    }

    /**
     * Commence à recevoir de l'audio
     */
    public void startReceiving() {
        try {
            while (receiving) {
                byte[] receiveBuffer = new byte[CHUNK_SIZE];
                DatagramPacket packet = new DatagramPacket(
                    receiveBuffer, 
                    receiveBuffer.length
                );
                
                udpSocket.receive(packet);
                
                // Traiter les données audio reçues
                processReceivedAudio(packet.getData(), packet.getLength());
            }
        } catch (SocketException e) {
            if (!e.getMessage().contains("Socket closed")) {
                logger.error("Erreur lors de la réception audio", e);
            }
        } catch (Exception e) {
            logger.error("Erreur lors de la réception audio", e);
        }
    }

    /**
     * Arrête la réception d'audio
     */
    public void stopReceiving() {
        receiving = false;
    }

    /**
     * Génère des données audio simulées
     */
    private void generateAudioData() {
        for (int i = 0; i < audioBuffer.length; i++) {
            // Générer un bruit blanc
            audioBuffer[i] = (byte) (Math.random() * 256 - 128);
        }
    }

    /**
     * Traite les données audio reçues
     */
    private void processReceivedAudio(byte[] data, int length) {
        // Ici, on pourrait implémenter la lecture des données audio
        // Pour l'instant, on les ignore simplement
    }
}

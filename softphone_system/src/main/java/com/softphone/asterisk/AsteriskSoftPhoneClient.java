package com.softphone.asterisk;

import com.softphone.client.SoftPhoneClient;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

/**
 * Client SoftPhone intégré avec Asterisk
 */
public class AsteriskSoftPhoneClient {
    private static final Logger logger = LoggerFactory.getLogger(AsteriskSoftPhoneClient.class);
    
    private SoftPhoneClient softPhoneClient;
    private AsteriskManager asteriskManager;
    private String extension;

    public AsteriskSoftPhoneClient(String extension, String softphoneServer, int udpPort,
                                   String asteriskHost, int asteriskPort, 
                                   String asteriskUser, String asteriskPass) {
        this.extension = extension;
        this.softPhoneClient = new SoftPhoneClient(extension, softphoneServer, udpPort);
        this.asteriskManager = new AsteriskManager(asteriskHost, asteriskPort, asteriskUser, asteriskPass);
    }

    public boolean connect() {
        logger.info("Connexion au système SoftPhone + Asterisk...");
        boolean softphoneOk = softPhoneClient.connect();
        boolean asteriskOk = asteriskManager.connect();
        
        return softphoneOk && asteriskOk;
    }

    public void makeCall(String targetNumber, String context) {
        asteriskManager.makeCall(extension, targetNumber, context);
    }

    public void disconnect() {
        softPhoneClient.disconnect();
        asteriskManager.disconnect();
        logger.info("Déconnecté du système");
    }

    public boolean isConnected() {
        return softPhoneClient.isConnected() && asteriskManager.isConnected();
    }
}

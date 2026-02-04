package com.softphone.asterisk;

import org.asteriskjava.manager.ManagerConnection;
import org.asteriskjava.manager.ManagerConnectionFactory;
import org.asteriskjava.manager.ManagerEventListener;
import org.asteriskjava.manager.action.OriginateAction;
import org.asteriskjava.manager.event.ManagerEvent;
import org.asteriskjava.manager.response.ManagerResponse;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

/**
 * Gestionnaire de connexion avec Asterisk via AMI
 */
public class AsteriskManager implements ManagerEventListener {
    private static final Logger logger = LoggerFactory.getLogger(AsteriskManager.class);
    
    private ManagerConnection managerConnection;
    private String hostname;
    private int port;
    private String username;
    private String password;
    private boolean connected = false;

    public AsteriskManager(String hostname, int port, String username, String password) {
        this.hostname = hostname;
        this.port = port;
        this.username = username;
        this.password = password;
    }

    public boolean connect() {
        try {
            ManagerConnectionFactory factory = new ManagerConnectionFactory(hostname, port, username, password);
            managerConnection = factory.createManagerConnection();
            managerConnection.addEventListener(this);
            managerConnection.login();
            connected = true;
            logger.info("Connecté à Asterisk: {}:{}", hostname, port);
            return true;
        } catch (Exception e) {
            logger.error("Erreur de connexion à Asterisk", e);
            return false;
        }
    }

    public void makeCall(String extension, String targetNumber, String context) {
        if (!connected) {
            logger.error("Non connecté à Asterisk");
            return;
        }

        try {
            OriginateAction originate = new OriginateAction();
            originate.setChannel("SIP/" + extension);
            originate.setContext(context);
            originate.setExten(targetNumber);
            originate.setPriority(1);
            originate.setCallerId(extension);
            originate.setTimeout(30000);

            ManagerResponse response = managerConnection.sendAction(originate);
            
            if (response != null) {
                logger.info("Appel initié via Asterisk: {} -> {}", extension, targetNumber);
            } else {
                logger.error("Erreur lors de l'initiation de l'appel");
            }
        } catch (Exception e) {
            logger.error("Erreur lors de l'appel", e);
        }
    }

    @Override
    public void onManagerEvent(ManagerEvent event) {
        logger.debug("Événement Asterisk: {}", event.getClass().getSimpleName());
    }

    public void disconnect() {
        if (managerConnection != null && connected) {
            try {
                managerConnection.logoff();
                connected = false;
                logger.info("Déconnecté d'Asterisk");
            } catch (Exception e) {
                logger.error("Erreur lors de la déconnexion", e);
            }
        }
    }

    public boolean isConnected() {
        return connected;
    }
}

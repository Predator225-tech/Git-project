package com.softphone.client.gui;

import com.softphone.client.SoftPhoneClient;
import javafx.application.Application;
import javafx.application.Platform;
import javafx.geometry.Insets;
import javafx.scene.Scene;
import javafx.scene.control.*;
import javafx.scene.layout.*;
import javafx.stage.Stage;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

/**
 * Interface graphique du SoftPhone
 */
public class SoftPhoneGUI extends Application {
    private static final Logger logger = LoggerFactory.getLogger(SoftPhoneGUI.class);
    
    private SoftPhoneClient client;
    private Label statusLabel;
    private TextArea logArea;
    private ComboBox<String> usersList;
    private Button callButton;
    private Button hangupButton;
    private TextField usernameField;
    private TextField serverField;
    private TextField portField;
    
    @Override
    public void start(Stage primaryStage) {
        primaryStage.setTitle("SoftPhone System");
        primaryStage.setWidth(600);
        primaryStage.setHeight(700);
        
        BorderPane root = new BorderPane();
        root.setPadding(new Insets(10));
        
        // Panneau de connexion
        root.setTop(createConnectionPanel());
        
        // Panneau des appels
        root.setCenter(createCallPanel());
        
        // Panneau de log
        root.setBottom(createLogPanel());
        
        Scene scene = new Scene(root);
        primaryStage.setScene(scene);
        primaryStage.show();
        
        // Fermer la connexion à la fermeture de l'application
        primaryStage.setOnCloseRequest(e -> {
            if (client != null && client.isConnected()) {
                client.disconnect();
            }
        });
    }

    /**
     * Crée le panneau de connexion
     */
    private VBox createConnectionPanel() {
        VBox panel = new VBox(10);
        panel.setStyle("-fx-border-color: #cccccc; -fx-border-radius: 5; -fx-padding: 10;");
        
        GridPane grid = new GridPane();
        grid.setHgap(10);
        grid.setVgap(10);
        
        // Nom d'utilisateur
        Label usernameLabel = new Label("Nom d'utilisateur:");
        usernameField = new TextField("utilisateur1");
        grid.add(usernameLabel, 0, 0);
        grid.add(usernameField, 1, 0);
        
        // Serveur
        Label serverLabel = new Label("Serveur:");
        serverField = new TextField("localhost");
        grid.add(serverLabel, 0, 1);
        grid.add(serverField, 1, 1);
        
        // Port UDP
        Label portLabel = new Label("Port UDP:");
        portField = new TextField("10000");
        grid.add(portLabel, 0, 2);
        grid.add(portField, 1, 2);
        
        // Boutons de connexion
        Button connectButton = new Button("Connexion");
        Button disconnectButton = new Button("Déconnexion");
        
        connectButton.setPrefWidth(150);
        disconnectButton.setPrefWidth(150);
        
        connectButton.setOnAction(e -> connectToServer());
        disconnectButton.setOnAction(e -> disconnectFromServer());
        
        HBox buttonBox = new HBox(10);
        buttonBox.getChildren().addAll(connectButton, disconnectButton);
        
        statusLabel = new Label("Déconnecté");
        statusLabel.setStyle("-fx-font-size: 14; -fx-font-weight: bold; -fx-text-fill: red;");
        
        panel.getChildren().addAll(
            new Label("Configuration:"),
            grid,
            buttonBox,
            new Separator(),
            statusLabel
        );
        
        return panel;
    }

    /**
     * Crée le panneau des appels
     */
    private VBox createCallPanel() {
        VBox panel = new VBox(10);
        panel.setPadding(new Insets(10));
        panel.setStyle("-fx-border-color: #cccccc; -fx-border-radius: 5;");
        
        // Liste des utilisateurs
        Label usersLabel = new Label("Utilisateurs en ligne:");
        usersList = new ComboBox<>();
        usersList.setPromptText("Sélectionnez un utilisateur");
        usersList.setPrefWidth(400);
        
        Button refreshButton = new Button("Rafraîchir");
        refreshButton.setOnAction(e -> {
            if (client != null && client.isConnected()) {
                client.listUsers();
            }
        });
        
        HBox usersBox = new HBox(10);
        usersBox.getChildren().addAll(usersList, refreshButton);
        
        // Boutons d'appel
        callButton = new Button("Appeler");
        hangupButton = new Button("Raccrocher");
        
        callButton.setPrefWidth(150);
        hangupButton.setPrefWidth(150);
        callButton.setDisable(true);
        hangupButton.setDisable(true);
        
        callButton.setOnAction(e -> initiateCall());
        hangupButton.setOnAction(e -> hangupCall());
        
        HBox callButtonBox = new HBox(10);
        callButtonBox.getChildren().addAll(callButton, hangupButton);
        
        panel.getChildren().addAll(
            usersLabel,
            usersBox,
            new Separator(),
            new Label("Contrôle d'appel:"),
            callButtonBox
        );
        
        VBox.setVgrow(panel, Priority.ALWAYS);
        return panel;
    }

    /**
     * Crée le panneau de log
     */
    private VBox createLogPanel() {
        VBox panel = new VBox(5);
        panel.setStyle("-fx-border-color: #cccccc; -fx-border-radius: 5; -fx-padding: 5;");
        
        Label logLabel = new Label("Journal d'événements:");
        logArea = new TextArea();
        logArea.setEditable(false);
        logArea.setPrefHeight(150);
        logArea.setWrapText(true);
        
        panel.getChildren().addAll(logLabel, logArea);
        return panel;
    }

    /**
     * Établit la connexion au serveur
     */
    private void connectToServer() {
        String username = usernameField.getText().trim();
        String server = serverField.getText().trim();
        String portStr = portField.getText().trim();
        
        if (username.isEmpty() || server.isEmpty() || portStr.isEmpty()) {
            addLog("Erreur: Veuillez remplir tous les champs");
            return;
        }
        
        try {
            int port = Integer.parseInt(portStr);
            client = new SoftPhoneClient(username, server, port);
            
            if (client.connect()) {
                statusLabel.setText("Connecté (" + username + ")");
                statusLabel.setStyle("-fx-font-size: 14; -fx-font-weight: bold; -fx-text-fill: green;");
                callButton.setDisable(false);
                usernameField.setDisable(true);
                serverField.setDisable(true);
                portField.setDisable(true);
                addLog("Connexion réussie en tant que: " + username);
            } else {
                addLog("Erreur: Impossible de se connecter au serveur");
            }
        } catch (NumberFormatException e) {
            addLog("Erreur: Port UDP invalide");
        }
    }

    /**
     * Déconnecte du serveur
     */
    private void disconnectFromServer() {
        if (client != null) {
            if (client.isConnected()) {
                client.disconnect();
            }
            client = null;
            statusLabel.setText("Déconnecté");
            statusLabel.setStyle("-fx-font-size: 14; -fx-font-weight: bold; -fx-text-fill: red;");
            callButton.setDisable(true);
            hangupButton.setDisable(true);
            usernameField.setDisable(false);
            serverField.setDisable(false);
            portField.setDisable(false);
            addLog("Déconnecté du serveur");
        }
    }

    /**
     * Initie un appel
     */
    private void initiateCall() {
        if (usersList.getValue() == null || usersList.getValue().isEmpty()) {
            addLog("Erreur: Sélectionnez un utilisateur");
            return;
        }
        
        String targetUser = usersList.getValue();
        if (client != null && client.isConnected()) {
            client.initiateCall(targetUser);
            addLog("Appel initié vers: " + targetUser);
            hangupButton.setDisable(false);
        }
    }

    /**
     * Raccroche l'appel en cours
     */
    private void hangupCall() {
        if (client != null && client.isConnected()) {
            client.hangupCall();
            hangupButton.setDisable(true);
            addLog("Appel terminé");
        }
    }

    /**
     * Ajoute un message au journal
     */
    private void addLog(String message) {
        Platform.runLater(() -> {
            String timestamp = java.time.LocalTime.now().format(
                java.time.format.DateTimeFormatter.ofPattern("HH:mm:ss")
            );
            logArea.appendText("[" + timestamp + "] " + message + "\n");
        });
    }

    public static void main(String[] args) {
        launch(args);
    }
}

package com.softphone.ui.controller;

import com.softphone.asterisk.AsteriskSoftPhoneClient;
import javafx.application.Platform;
import javafx.fxml.FXML;
import javafx.scene.control.*;
import javafx.scene.control.cell.PropertyValueFactory;
import javafx.scene.layout.GridPane;
import javafx.scene.layout.HBox;
import javafx.scene.layout.VBox;
import javafx.geometry.Insets;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;
import java.time.LocalDateTime;
import java.time.format.DateTimeFormatter;

/**
 * Contrôleur principal de l'application SoftPhone
 */
public class MainController {
    private static final Logger logger = LoggerFactory.getLogger(MainController.class);
    
    @FXML private Label statusLabel;
    @FXML private Label userInfoLabel;
    @FXML private TextField phoneNumberField;
    @FXML private TextField extensionField;
    @FXML private TextField usernameField;
    @FXML private PasswordField passwordField;
    @FXML private ComboBox<String> contextCombo;
    @FXML private Button connectButton;
    @FXML private Button disconnectButton;
    @FXML private Button callButton;
    @FXML private Button hangupButton;
    @FXML private TableView<CallLog> callHistoryTable;
    @FXML private Label callDurationLabel;
    @FXML private Label callStatusLabel;
    @FXML private VBox dialpadContainer;
    
    private AsteriskSoftPhoneClient softPhoneClient;
    private long callStartTime;
    private boolean inCall = false;

    @FXML
    public void initialize() {
        logger.info("Initialisation du contrôleur principal");
        setupUI();
        setupEventHandlers();
        setupCallHistory();
    }

    private void setupUI() {
        // Désactiver les boutons d'appel par défaut
        callButton.setDisable(true);
        hangupButton.setDisable(true);
        disconnectButton.setDisable(true);
        phoneNumberField.setDisable(true);

        // Ajouter les contextes Asterisk disponibles
        contextCombo.getItems().addAll(
            "from-internal",
            "from-external",
            "from-sip"
        );
        contextCombo.setValue("from-internal");

        // Mettre à jour le statut initial
        updateStatus("Déconnecté", false);
        
        // Créer le clavier numérique
        createDialpad();
    }

    private void createDialpad() {
        GridPane dialpad = new GridPane();
        dialpad.setHgap(5);
        dialpad.setVgap(5);
        dialpad.setPadding(new Insets(10));
        
        String[] buttons = {
            "1", "2", "3",
            "4", "5", "6",
            "7", "8", "9",
            "*", "0", "#"
        };

        int index = 0;
        for (int row = 0; row < 4; row++) {
            for (int col = 0; col < 3; col++) {
                Button btn = new Button(buttons[index]);
                btn.setPrefSize(60, 60);
                btn.setStyle("-fx-font-size: 16; -fx-font-weight: bold;");
                String digit = buttons[index];
                btn.setOnAction(e -> phoneNumberField.appendText(digit));
                dialpad.add(btn, col, row);
                index++;
            }
        }

        // Bouton Retour
        Button backspace = new Button("←");
        backspace.setPrefSize(60, 60);
        backspace.setStyle("-fx-font-size: 16;");
        backspace.setOnAction(e -> {
            String text = phoneNumberField.getText();
            if (text.length() > 0) {
                phoneNumberField.setText(text.substring(0, text.length() - 1));
            }
        });
        dialpad.add(backspace, 0, 4, 3, 1);
        
        dialpadContainer.getChildren().add(dialpad);
    }

    private void setupEventHandlers() {
        connectButton.setOnAction(e -> handleConnect());
        disconnectButton.setOnAction(e -> handleDisconnect());
        callButton.setOnAction(e -> handleCall());
        hangupButton.setOnAction(e -> handleHangup());
    }

    private void setupCallHistory() {
        // Configuration de la table d'historique des appels
        TableColumn<CallLog, String> numberCol = new TableColumn<>("Numéro");
        numberCol.setCellValueFactory(new PropertyValueFactory<>("phoneNumber"));
        
        TableColumn<CallLog, String> typeCol = new TableColumn<>("Type");
        typeCol.setCellValueFactory(new PropertyValueFactory<>("callType"));
        
        TableColumn<CallLog, String> dateCol = new TableColumn<>("Date/Heure");
        dateCol.setCellValueFactory(new PropertyValueFactory<>("dateTime"));
        
        TableColumn<CallLog, String> durationCol = new TableColumn<>("Durée");
        durationCol.setCellValueFactory(new PropertyValueFactory<>("duration"));

        callHistoryTable.getColumns().addAll(numberCol, typeCol, dateCol, durationCol);
    }

    private void handleConnect() {
        String extension = extensionField.getText().trim();
        String username = usernameField.getText().trim();
        String password = passwordField.getText().trim();

        if (extension.isEmpty() || username.isEmpty() || password.isEmpty()) {
            showAlert("Erreur", "Veuillez remplir tous les champs", Alert.AlertType.WARNING);
            return;
        }

        // Créer le client
        softPhoneClient = new AsteriskSoftPhoneClient(
            extension, "localhost", 10000,
            "localhost", 5038,
            username, password
        );

        if (softPhoneClient.connect()) {
            updateStatus("Connecté (" + extension + ")", true);
            callButton.setDisable(false);
            disconnectButton.setDisable(false);
            phoneNumberField.setDisable(false);
            connectButton.setDisable(true);
            extensionField.setDisable(true);
            usernameField.setDisable(true);
            passwordField.setDisable(true);
        } else {
            showAlert("Erreur", "Impossible de se connecter", Alert.AlertType.ERROR);
        }
    }

    private void handleDisconnect() {
        if (softPhoneClient != null) {
            softPhoneClient.disconnect();
        }
        
        updateStatus("Déconnecté", false);
        callButton.setDisable(true);
        hangupButton.setDisable(true);
        disconnectButton.setDisable(true);
        phoneNumberField.setDisable(true);
        connectButton.setDisable(false);
        extensionField.setDisable(false);
        usernameField.setDisable(false);
        passwordField.setDisable(false);
    }

    private void handleCall() {
        String phoneNumber = phoneNumberField.getText().trim();
        String context = contextCombo.getValue();

        if (phoneNumber.isEmpty()) {
            showAlert("Erreur", "Veuillez entrer un numéro", Alert.AlertType.WARNING);
            return;
        }

        if (softPhoneClient != null && softPhoneClient.isConnected()) {
            softPhoneClient.makeCall(phoneNumber, context);
            inCall = true;
            callStartTime = System.currentTimeMillis();
            callStatusLabel.setText("En appel avec: " + phoneNumber);
            callStatusLabel.setStyle("-fx-text-fill: #4CAF50;");
            callButton.setDisable(true);
            hangupButton.setDisable(false);
            phoneNumberField.setDisable(true);
            
            // Démarrer le chrono
            startCallTimer();
            
            // Ajouter à l'historique
            addCallLog(phoneNumber, "Sortant");
        }
    }

    private void handleHangup() {
        if (inCall) {
            inCall = false;
            callStatusLabel.setText("Appel terminé");
            callStatusLabel.setStyle("-fx-text-fill: #f44336;");
            callButton.setDisable(false);
            hangupButton.setDisable(true);
            phoneNumberField.setDisable(false);
            callDurationLabel.setText("Durée: 0s");
        }
    }

    private void startCallTimer() {
        Thread timerThread = new Thread(() -> {
            while (inCall) {
                try {
                    Thread.sleep(1000);
                    long elapsed = (System.currentTimeMillis() - callStartTime) / 1000;
                    long minutes = elapsed / 60;
                    long seconds = elapsed % 60;
                    
                    Platform.runLater(() -> 
                        callDurationLabel.setText(String.format("Durée: %02d:%02d", minutes, seconds))
                    );
                } catch (InterruptedException e) {
                    break;
                }
            }
        });
        timerThread.setDaemon(true);
        timerThread.start();
    }

    private void addCallLog(String phoneNumber, String type) {
        DateTimeFormatter formatter = DateTimeFormatter.ofPattern("dd/MM/yyyy HH:mm:ss");
        String dateTime = LocalDateTime.now().format(formatter);
        CallLog log = new CallLog(phoneNumber, type, dateTime, "En cours");
        callHistoryTable.getItems().add(0, log);
    }

    private void updateStatus(String status, boolean connected) {
        statusLabel.setText(status);
        if (connected) {
            statusLabel.setStyle("-fx-text-fill: #4CAF50; -fx-font-weight: bold;");
        } else {
            statusLabel.setStyle("-fx-text-fill: #f44336; -fx-font-weight: bold;");
        }
    }

    private void showAlert(String title, String message, Alert.AlertType type) {
        Alert alert = new Alert(type);
        alert.setTitle(title);
        alert.setHeaderText(null);
        alert.setContentText(message);
        alert.showAndWait();
    }

    /**
     * Classe interne pour l'historique des appels
     */
    public static class CallLog {
        private String phoneNumber;
        private String callType;
        private String dateTime;
        private String duration;

        public CallLog(String phoneNumber, String callType, String dateTime, String duration) {
            this.phoneNumber = phoneNumber;
            this.callType = callType;
            this.dateTime = dateTime;
            this.duration = duration;
        }

        public String getPhoneNumber() { return phoneNumber; }
        public String getCallType() { return callType; }
        public String getDateTime() { return dateTime; }
        public String getDuration() { return duration; }
    }
}

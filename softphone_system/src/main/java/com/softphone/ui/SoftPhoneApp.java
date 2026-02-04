package com.softphone.ui;

import javafx.application.Application;
import javafx.fxml.FXMLLoader;
import javafx.scene.Scene;
import javafx.scene.image.Image;
import javafx.stage.Stage;
import org.slf4j.Logger;
import org.slf4j.LoggerFactory;

/**
 * Application principale JavaFX pour le SoftPhone
 */
public class SoftPhoneApp extends Application {
    private static final Logger logger = LoggerFactory.getLogger(SoftPhoneApp.class);

    @Override
    public void start(Stage primaryStage) {
        try {
            // Charger le contrôleur principal
            FXMLLoader loader = new FXMLLoader(getClass().getResource("/fxml/main.fxml"));
            javafx.scene.Parent root = loader.load();

            // Créer la scène
            Scene scene = new Scene(root);
            scene.getStylesheets().add(getClass().getResource("/css/style.css").toExternalForm());

            // Configurer la fenêtre
            primaryStage.setTitle("SoftPhone Pro - Système d'appels VoIP");
            primaryStage.setWidth(1200);
            primaryStage.setHeight(800);
            primaryStage.setMinWidth(900);
            primaryStage.setMinHeight(600);
            primaryStage.setScene(scene);
            primaryStage.show();

            logger.info("Application SoftPhone démarrée");
        } catch (Exception e) {
            logger.error("Erreur au démarrage de l'application", e);
            e.printStackTrace();
        }
    }

    public static void main(String[] args) {
        launch(args);
    }
}

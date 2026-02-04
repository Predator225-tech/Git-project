package com.softphone.web.controller;

import org.springframework.web.bind.annotation.*;
import org.springframework.http.ResponseEntity;
import org.springframework.http.HttpStatus;
import java.util.*;

@RestController
@RequestMapping("/api/softphone")
@CrossOrigin(origins = "*")
public class SoftPhoneController {

    private Map<String, Object> callState = new HashMap<>();
    private List<Map<String, Object>> callHistory = new ArrayList<>();

    @PostMapping("/connect")
    public ResponseEntity<?> connect(@RequestBody Map<String, String> request) {
        String extension = request.get("extension");
        String username = request.get("username");
        String password = request.get("password");

        Map<String, Object> response = new HashMap<>();
        response.put("success", true);
        response.put("message", "Connecté avec succès");
        response.put("extension", extension);

        return ResponseEntity.ok(response);
    }

    @PostMapping("/disconnect")
    public ResponseEntity<?> disconnect() {
        Map<String, Object> response = new HashMap<>();
        response.put("success", true);
        response.put("message", "Déconnecté");

        return ResponseEntity.ok(response);
    }

    @PostMapping("/call")
    public ResponseEntity<?> call(@RequestBody Map<String, String> request) {
        String phoneNumber = request.get("phoneNumber");
        String context = request.get("context");

        Map<String, Object> callData = new HashMap<>();
        callData.put("phoneNumber", phoneNumber);
        callData.put("context", context);
        callData.put("timestamp", System.currentTimeMillis());
        callData.put("status", "En appel");

        callState = callData;

        Map<String, Object> response = new HashMap<>();
        response.put("success", true);
        response.put("message", "Appel initié vers " + phoneNumber);
        response.put("callId", UUID.randomUUID().toString());

        return ResponseEntity.ok(response);
    }

    @PostMapping("/hangup")
    public ResponseEntity<?> hangup() {
        if (!callState.isEmpty()) {
            callHistory.add(new HashMap<>(callState));
            callState.clear();
        }

        Map<String, Object> response = new HashMap<>();
        response.put("success", true);
        response.put("message", "Appel terminé");

        return ResponseEntity.ok(response);
    }

    @GetMapping("/call-history")
    public ResponseEntity<?> getCallHistory() {
        return ResponseEntity.ok(callHistory);
    }

    @GetMapping("/status")
    public ResponseEntity<?> getStatus() {
        Map<String, Object> status = new HashMap<>();
        status.put("connected", true);
        status.put("inCall", !callState.isEmpty());
        status.put("currentCall", callState);

        return ResponseEntity.ok(status);
    }
}

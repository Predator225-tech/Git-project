package com.softphone.web;

import org.springframework.boot.SpringApplication;
import org.springframework.boot.autoconfigure.SpringBootApplication;
import org.springframework.context.annotation.ComponentScan;

@SpringBootApplication
@ComponentScan(basePackages = {"com.softphone"})
public class SoftPhoneWebApplication {
    public static void main(String[] args) {
        SpringApplication.run(SoftPhoneWebApplication.class, args);
    }
}

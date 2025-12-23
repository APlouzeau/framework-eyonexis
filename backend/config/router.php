<?php

// Connexion
$router->addRoute("POST", "/api/login", "ControllerUserAuth", "login");
$router->addRoute("POST", "/api/logout", "ControllerUserAuth", "logout");
$router->addRoute("POST", "/api/register", "ControllerUserRegistration", "register");
$router->addRoute("POST", "/api/verifyConnect", "ControllerUser", "verifyConnect");

//Users
$router->addRoute("GET", "/api/users", "ControllerUserManagement", "listUsers");
$router->addRoute("POST", "/api/userInformations", "ControllerUserProfile", "getUserInformations");
$router->addRoute("POST", "/api/updateUserProfile", "ControllerUserProfile", "updateUserProfile");
$router->addRoute("GET", "/api/verify-email/{token}", "ControllerUserRegistration", "verifyEmail");
$router->addRoute("POST", "/api/getWallet", "ControllerOrder", "getWallet");
$router->addRoute("POST", "/api/updateUserProfile", "ControllerUserProfile", "updateUserProfile");
$router->addRoute("POST", "/api/updateUserPassword", "ControllerUserProfile", "updateUserPassword");
$router->addRoute("POST", "/api/forgetedPassword", "ControllerUserAuth", "forgetedPassword");
$router->addRoute("GET", "/api/reset-password/{token}", "ControllerUserAuth", "resetPasswordLink");
$router->addRoute("POST", "/api/resetPassword", "ControllerUserAuth", "resetPassword");

//Lessons
$router->addRoute("GET", "/api/lessons", "ControllerLesson", "getAllLessons");
$router->addRoute("GET", "/api/offre-de-cours/{slug}", "ControllerLesson", "getLessonByName");
$router->addRoute("POST", "/api/getAllLessonsWithPrices", "ControllerLesson", "getAllLessonsWithPrices");

//Events
$router->addRoute("POST", "/api/createEvent", "ControllerCalendar", "createEvent");
$router->addRoute("POST", "/api/listEvents", "ControllerCalendar", "listEvents");
$router->addRoute("POST", "/api/checkDeleteEvent", "ControllerCalendar", "checkDeleteEvent");
$router->addRoute("POST", "/api/deleteEvent", "ControllerCalendar", "deleteEvent");
$router->addRoute("POST", "/api/getAvailableTimeSlots", "ControllerCalendar", "getAvailablesTimeSlots");
$router->addRoute("POST", "/api/sendMailToAlertForNextAppointment", "ControllerMail", "sendMailToAlertForNextAppointment");
$router->addRoute("POST", "/api/prepareRepayment", "ControllerOrder", "prepareRepayment");
$router->addRoute("POST", "/api/deleteWaitingEvent", "ControllerCalendar", "checkWaitingEvents");
$router->addRoute("GET", "/api/createInstantRoom", "ControllerVisio", "createInstantRoom");

//Google
$router->addRoute("POST", "/api/handleGoogleNotification", "ControllerGoogle", "handleGoogleNotification");
$router->addRoute("POST", "/api/setupGoogleWatch", "ControllerGoogle", "setupCalendarWatch");

//Visio
$router->addRoute("POST", "/api/createInstantVisio", "ControllerVisio", "createInstantRoom");

//Stripe
$router->addRoute("POST", "/api/checkout-session", "ControllerOrder", "checkout");
$router->addRoute("POST", "/api/payment-status", "ControllerOrder", "status");
$router->addRoute("POST", "/api/verify-payment", "ControllerOrder", "verifyPayment");


//debug
$router->addRoute("GET", "/api/debug", "ControllerDebug", "debug");
$router->addRoute("GET", "/api/debugBdd", "ControllerDebug", "debugBdd");

//admin
$router->addRoute("POST", "/api/getInvoices", "ControllerInvoice", "getInvoices");
$router->addRoute("POST", "/api/setInvoiced", "ControllerInvoice", "setInvoiced");

//packs
$router->addRoute("POST", "/api/orderPacks", "ControllerOrder", "orderPacks");


//tests
$router->addRoute("GET", "/api/sql", "ModelPrices", "getPriceByEventId");

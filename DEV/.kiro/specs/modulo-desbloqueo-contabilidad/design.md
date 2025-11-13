# Design Document

## Overview

El módulo de Desbloqueo de Módulos es un sistema administrativo que permite gestionar el acceso temporal a módulos operativos del sistema contable. Se implementa siguiendo la arquitectura MVC del framework CoffeeSoft, utilizando el pivote admin como base estructural. El módulo se ubicará en `contabilidad/administrador` y proporcionará una interfaz intuitiva con tabs para gestionar desbloqueos, cuentas de ventas, formas de pago, clientes y compras.

## Architecture

### System Architecture

```
┌─────────────────────────────────────────────────────────────┐
│                    Frontend Layer (JS)                       │
│  ┌──────────────┐  ┌──────────────┐  ┌──────────────┐      │
│  │ App (Main)   │  │ SalesAccount │  │ PaymentForms │      │
│  │ - Unlock Tab │  │ - Sales Tab  │  │ - Payment Tab│      │
│  └──────────────┘  └──────────────┘  └──────────────┘      │
│  ┌──────────────┐  ┌──────────────┐                         │
│  │ Customers    │  │ Purchases    │                         │
│  │ - Client Tab │  │ - Purchase   │                         │
│  └─────────
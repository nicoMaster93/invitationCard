# Backend - invitationCard

Este módulo contiene la lógica del negocio, almacenamiento y generación de tarjetas de invitación.

## Tecnologías

- **Lenguaje**: Node.js
- **Framework**: Express.js
- **Base de Datos**: MongoDB (o la base de datos configurada)
- **Autenticación**: JWT (si aplica)

## Instalación

1. Navega a la carpeta `back`:
   ```bash
   cd back
   ```
2. Instala las dependencias:
   ```bash
   npm install
   ```

## Uso

1. Configura las variables de entorno en un archivo `.env`:
   ```
   PORT=5000
   DB_URI=<tu-uri-de-base-de-datos>
   JWT_SECRET=<tu-secreto-jwt>
   ```
2. Inicia el servidor:
   ```bash
   npm start
   ```

## Estructura del Código

```
back/
├── src/
│   ├── controllers/  # Controladores de las rutas
│   ├── models/       # Modelos de la base de datos
│   ├── routes/       # Definición de las rutas
│   ├── middlewares/  # Middlewares personalizados
│   └── app.js        # Configuración principal del servidor
└── config/           # Configuración de la base de datos y variables
```

## Scripts Disponibles

- `npm start`: Inicia el servidor en modo producción.
- `npm run dev`: Inicia el servidor en modo desarrollo.
- `npm test`: Ejecuta las pruebas (si están configuradas).

## Contribuciones

Si deseas mejorar el backend, por favor abre un issue o envía un pull request.

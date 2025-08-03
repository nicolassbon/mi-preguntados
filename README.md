# Proyecto Mi-Preguntados

## Descripción
Mi-Preguntados es un juego de preguntas y respuestas estilo trivia desarrollado en PHP con arquitectura MVC. Los usuarios pueden registrarse, jugar partidas respondiendo preguntas de diferentes categorías y competir por los mejores puntajes en el ranking.

## Contexto y Origen del Proyecto
Este proyecto está basado en un trabajo académico desarrollado para una materia de programación web. La versión original contemplaba funcionalidades básicas y requisitos mínimos para cumplir con los objetivos educativos.
A partir de esa base, se incorporaron múltiples implementaciones adicionales para enriquecer la experiencia del usuario y ampliar las capacidades del juego, tales como:
- Integración con MercadoPago para la compra de ayudas ("trampitas").
- Sistema de desafíos entre usuarios.
- Verificación de usuarios vía email.
- Geolocalización opcional para usuarios.

## Documentación
La explicación detallada del proyecto base está disponible en el siguiente documento:

[Documento base del proyecto (PDF)](docs/proyecto_base.pdf)

## Características Principales

- **Sistema de usuarios**: Registro con verificación por email, login y perfiles personalizados
- **Categorías temáticas**: 8 categorías diferentes (Gastronomía, Historia, Deportes, Tecnología, etc.)
- **Roles de usuario**: Jugador, Editor y Administrador con diferentes permisos
- **Ruleta de categorías**: Selección aleatoria de la próxima categoría
- **Ranking global**: Comparativa de puntajes entre jugadores
- **Sistema de "trampitas"**: Ayudas que pueden comprarse mediante MercadoPago
- **Sugerencias y reportes**: Los usuarios pueden sugerir nuevas preguntas o reportar incorrectas
- **Ubicación geográfica**: Registro opcional de la ubicación de los usuarios
- **Desafíos entre jugadores**: Modo multijugador para competir directamente
- **Reportes administrativos**: Estadísticas y métricas en formato PDF

## Tecnologías Utilizadas

- **Backend**: PHP 8.2
- **Base de datos**: MySQL (MariaDB 10.4)
- **Frontend**: HTML, CSS, JavaScript
- **Servidor**: Apache (XAMPP)
- **Plantillas**: Mustache
- **Dependencias**: 
  - mustache/mustache: Sistema de plantillas
  - phpmailer/phpmailer: Envío de emails
  - dompdf/dompdf: Generación de PDFs
  - mercadopago/dx-php: Integración con pagos

## Requisitos

- XAMPP con PHP 8.2+ y MySQL/MariaDB
- Composer para gestión de dependencias

## Instalación

1. Clonar el repositorio en la carpeta `htdocs` de XAMPP
   ```
   git clone https://github.com/tu-usuario/mi-preguntados.git
   ```

2. Instalar dependencias con Composer
   ```
   composer install
   ```

3. Crear base de datos e importar el script
   - Crear una base de datos llamada `mi_preguntados`
   - Importar el archivo `sqlscript/mi_preguntados.sql`

4. Configurar el archivo de conexión
   - Copiar `configuration/config.example.ini` a `configuration/config.ini`
   - Modificar los datos de conexión según tu configuración

5. Iniciar Apache y MySQL en XAMPP

6. Acceder a la aplicación
   ```
   http://localhost/mi-preguntados/
   ```

## Estructura del Proyecto

- `configuration/`: Archivos de configuración
- `public/`: Recursos estáticos (CSS, imágenes, sonidos)
- `sqlscript/`: Scripts para crear la base de datos
- `src/`: Código fuente
  - `controller/`: Controladores de la aplicación
  - `model/`: Modelos de datos
  - `core/`: Componentes centrales (DB, Router, etc.)
  - `helpers/`: Funciones auxiliares
- `vendor/`: Dependencias de Composer
- `view/`: Plantillas Mustache

## Flujo de Juego

1. Los usuarios se registran y verifican su cuenta por email
2. En el lobby pueden iniciar nuevas partidas o ver desafíos
3. La ruleta determina la categoría de la próxima pregunta
4. Se responde a preguntas de opción múltiple acumulando puntos
5. Las "trampitas" pueden usarse para responder correctamente
6. El puntaje final se registra en el ranking global

## Roles de Usuario

- **Jugador**: Puede jugar partidas, sugerir preguntas y reportar errores
- **Editor**: Puede gestionar preguntas, aprobar sugerencias y evaluar reportes
- **Administrador**: Acceso a estadísticas, informes y gestión completa del sistema

## Monetización

El juego implementa un sistema de "trampitas" que los usuarios pueden comprar por $1 cada una mediante la integración con MercadoPago. Estas trampitas permiten responder correctamente a una pregunta sin conocer la respuesta.

.env 
Para usar la base de datos local 
1. Descargar el script de: https://limewire.com/d/J3Cue#101VxcbqoB Y correrlo en psql local
2. Crear un usuario en la bd y hashear la contraseña en 2y con el comando (Cambiar el espacio CONTRASEÑA A HASHEAR por la contraseña): php -r "echo preg_replace('/^\$2b\$/', '\$2y\$', password_hash('CONTRASEÑA A HASHEAR', PASSWORD_BCRYPT));"
3. Hacer un update del usuario creado cambiando la contraseña por el hash de la misma
4. Comentar el DB_URL
5. Descomentar todos los espacios de PostgreSQL Local

Para usar en la base de datos web
1. Enviar correo a alecordero2610@gmail.com para solicitar un usuario
2. Asegurarse de que todos los atributos de PostgreSQL Local estén comentados
3. Asegurarse de que DB_URL NO esté comentado


APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:ZMtgIc0JEmqZ16i5iozMwbd4idr+9p7XY3ljvxOvHzE=
APP_DEBUG=true
APP_URL=http://localhost
APP_LOCALE=en
APP_FALLBACK_LOCALE=en

LOG_CHANNEL=stack
LOG_LEVEL=debug

# PostgreSQL Render
DB_CONNECTION=pgsql
DB_URL=postgresql://root:rtw55K2VVIxtQJbGk3tu95MrUZTeDC37@dpg-d259dl63jp1c73d3p9vg-a.virginia-postgres.render.com/proyectointegradorbd

# PostgreSQL Local
#DB_HOST=127.0.0.1
#DB_PORT=5432
#DB_DATABASE=prueba
#DB_USERNAME=postgres
#DB_PASSWORD=

SESSION_DRIVER=file
QUEUE_CONNECTION=sync
CACHE_STORE=file
SESSION_DRIVER=file
SESSION_LIFETIME=120
SESSION_ENCRYPT=false
SESSION_PATH=/
SESSION_DOMAIN=null
APP_KEY=base64:ZMtgIc0JEmqZ16i5iozMwbd4idr+9p7XY3ljvxOvHzE=

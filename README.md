# Mini-App

- [ModRewrite](#-modrewrite)
- [Error 404](#-error-404)
- [Base de datos](#-base-de-datos)
  * [MySQLi](#mysqli)
  * [SQLite](#sqlite)
  * [Uso de la base de datos](#uso-de-la-base-de-datos)
- [Secciones](#-secciones)
  * [Variables en las secciones](#variables-en-las-secciones)

Es una renovación de la idea de [**Mini-Api**](https://github.com/nicolasegp/mini-api) para crear proyectos pequeños en PHP de forma fácil y con poco código.

## • ModRewrite

Si desea implementar el `mod_rewite` en su proyecto para que su URL cambien de `proyecto.com/index.php/seccion` a `proyecto.com/seccion` revisar la wiki de [**Servidores Apache**](https://github.com/nicolasegp/mini-app/wiki/Servidores-Apache) o [**Servidores Nginx**](https://github.com/nicolasegp/mini-app/wiki/Servidores-Nginx)

Adicional a esta configuración se debe agregar el parámetro de mod_rewrite en la configuración de su proyecto de la siguiente manera:

```php
->config('mod_rewrite', true)
```

## • Error 404

Para configurar una página personalizada de error 404 se debe asignar el parámetro "404" y la ruta del archivo en la configuración del proyecto, en caso de no configurarlo la app dará un mensaje 404 sin mostrar nada, se puede configurar de la siguiente manera:

```php
->config('404', 'app/404.html')
```

## • Base de datos

Actualmente está configurada solamente MySQLi y SQLite como drivers de base de datos, pero en `app.php` en la función `exeConfig()` se pueden agregar mas como PDO o MongoDB por ejemplo.

Para poder realizar una conexión se debe configurar el parámetro que corresponda por ejemplo

### MySQLi

```php
->config('mysqli', 'localhost', 'user', 'pass', 'database')
```

### SQLite

```php
->config('sqlite', 'archivo.db')
```

### Uso de la base de datos

dentro del contexto de ruta se debe usar el primer parámetro el cual está reservado para `$app` dentro de esta variable se encuentra el objecto de la base de datos `$app->db`, por ejemplo:

```php
->route('api/update', function($app) {
	$app->db->query("UPDATE `tabla` SET `nombre` = 'John Doe' WHERE `id`='1'");
	$app->db->close();
})
```

## • Secciones

Para crear una sección debemos usar la función `route()`, solo posee 2 parámetros. El primero es la ruta que puede contener expresiones regulares o no. El segundo es una función donde sus parámetros son dinámicos y se definen según lo solicitado en la ruta, exceptuando el primer parámetro que es reservado para la variable `$app` la cual nos ayuda a ingresar contenido de la class a nuestro encapsulamiento.

```php
->route('seccion', function($app) {
	...
})
```

### Variables en las secciones

Si deseamos interactuar con la sección lo podemos hacer asignando variables mediante expresiones regulares

```php
->route('blog/(\d+)/([\w-]+)', function($app, $id, $slug) {
	echo "Hola, mi ID es {$id} y mi Slug es {$slug}";
})
```

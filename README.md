# api.shoptive.com

## This API is based:
  * http://www.hermosaprogramacion.com/2015/10/servicio-web-restful-android-php-mysql-json/
  * https://web.archive.org/web/20130910164802/http://www.gen-x-design.com/archives/create-a-rest-api-with-php/
  * http://programandolo.blogspot.mx/2013/08/ejemplo-php-de-servicio-restful-parte-1.html
  * http://coreymaynard.com/blog/creating-a-restful-api-with-php/

  
  v1/rest.php
    Código principal, es donde se hacen los request. Además de contener la función que regresa las tiendas cercanas
    a la coordenada, utilizando Algolia.

  v1/poi/NearestPointsOfInterest.php
    Obtiene los puntos de interes (tiendas) cercanas a una coordenada, no se utilizó Algolia en esta parte,
    se hace consulta a MySQL.

  v1/prueba.php
    Envia una petición a través de un formulario para obtener las tiendas.

  curl http://api.shoptive.com/v1/getStores/coordinate=33.1270251,-96.6595335
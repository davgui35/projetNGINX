# projetNGINX

## Utils

- PHP => 7.4
- Nginx
- MariaDB
- PhpMyAdmin
- Jenkins
- SonarQube

### Devenir super admin

```bash
su -
sudo /etc/sudoers
```

nom ALL=(ALL:ALL) ALL

### LEMP est un acronyme signifiant :

 
L : Linux – les serveurs fonctionnent nativement sous une distribution Linux
E : EngineX, souvent appelé Nginx – serveur web qui recevra les requêtes de vos visiteurs et les redirigera vers le dossier hébergeant votre site
M : MariaDB or Mysql – base de données qui stockera toutes les données (contenus, utilisateurs, configuration, médias..) dont vous aurez besoin sur votre site
P : PHP – langage de programmation que vous utiliserez pour développer votre site

Avant de commencer, assurez vous que votre serveur est à jour :

```bash
sudo apt-get update && sudo apt-get upgrade

```

### INSTALLATION SERVER

### NGINX

```bash
sudo apt install nginx
```

 
_Problème avec Apache_
```bash
sudo /etc/init.d/apache2 stop
```

### Démarrer les services NGINX 

```bash
sudo service nginx restart
sudo systemctl enable nginx (connexion automatique)
sudo service nginx stop
```

### PHP

```bash
apt install php7.4-fpm php7.4-mysql php7.4-common php7.4-gd php7.4-json php7.4-cli php7.4-curl php7.4-xml php7.4-zip php7.4-mbstring
```

Une fois installé, changez ces valeurs :

```bash
sudo nano /etc/php/7.4/fpm/pool.d/www.conf
```


pm.max_children = 10
pm.max_requests = 200

```bash
sudo nano /etc/php/7.4/fpm/php.ini
```

_Changement de timeZone_
date.timezone = America/Cayenne //Indiquez votre timezone

upload_max_filesize = 8M
max_execution_time = 60
max_input_vars = 5000
extensions=pdo_mysql

Redémarrez maintenant le service pour appliquer les changements :

```bash
sudo service php7.4-fpm restart
```
 
### MARIADB

```bash
apt install mariadb-server
mysql_secure_installation
--
1.enter
--
2.y
--
3.Y
--
4.Y
--
5.Y
--
6.Y

```
 
```bash
sudo nano /etc/mysql/mariadb.conf.d/50-server.cnf
```
[mysqld]
query_cache_limit       = 2M
query_cache_size        = 32M
innodb_buffer_pool_instances = 1
innodb_buffer_pool_size = 79M

[mariadb]
aria_pagecache_buffer_size = 2M

Enfin, redémarrez le service :

```bash
sudo service mariadb restart
```

### Configurer un site web sous LEMP
 
Maintenant que vous avons installé notre stack LEMP, nous allons pouvoir configurer notre premier site web en suivant ces étapes :

1.Créer ou copier un site web

2.Créer un dossier de logs

3.Créer un hôte virtuel dans Nginx

4.Activer cet hôte virtuel

### Créer ou copier un site web

Commençons par créer notre site. A des fins de simplification, nous utiliserons ici juste un fichier nommé index contenant tous les paramètres de notre configuration. Une fois celui créé, il faudra lui donner les droits d’accès et de modification à l’utilisateur www-data (utilisateur par défaut dans Nginx). Le plus simple est d’attribuer la propriété du dossier à cet utilisateur (nous sécuriserons d’avantage ces permissions dans la suite de cet article) :

 
```bash
sudo mkdir /var/www/monsite
sudo nano /var/www/monsite/index.php
``` 
<?php phpinfo(); ?>

```bash
sudo chown www-data -R /var/www/monsite/
```
Ce fichier est un fichier sensible comprenant des informations interne à votre serveur. Ne laissez pas celui ci accessible après cette installation, et pensez bien à le supprimer une fois testé.

Si vous avez déjà un site que vous voulez copier, tapez la commande suivante :

```bash
cp -R website/ /var/www/
```

### Créer un dossier de logs

Ensuite, créons un dossier dans lequel tous les logs applicatifs (journaux d’accès et d’erreurs) seront stockés. De même que précédemment, attribuons sa propriété à l’utilisateur www-data :

```bash
sudo mkdir /var/log/nginx/monsite/
sudo chown -R www-data /var/log/nginx/monsite/
```

### Créer un hôte virtuel dans Nginx

Tout d’abord, désactivons le site configuré par défaut dans Nginx :

```bash
sudo unlink /etc/nginx/sites-enabled/default
``` 

Puis créons un hote virtuel minimal (nous l’améliorerons par la suite) pour simplement afficher notre site :

```bash
sudo nano /etc/nginx/sites-available/monsite.conf

```

server {

    server_name monsite;

    listen 80;

    port_in_redirect off;

    access_log /var/log/nginx/monsite/access.log;

    error_log /var/log/nginx/monsite/error.log error;

 

    root /var/www/monsite/;

    location / {

        index index.php index.html index.htm;

        try_files $uri $uri/ =404;

    }

    location ~ \.php$ {

        try_files $uri =404;

        include fastcgi_params;

        fastcgi_pass unix:/run/php/php7.4-fpm.sock;

        fastcgi_split_path_info ^(.+\.php)(.*)$;

        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;

    }

}

### Activer un hôte virtuel dans Nginx

Enfin, activons notre hôte et redémarrons Nginx :

```bash
sudo ln -s /etc/nginx/sites-available/monsite.conf /etc/nginx/sites-enabled/monsite.conf
sudo service nginx restart
```

A ce stade, nous avons un serveur fonctionnel utilisant HTTPS. Avant de l’utiliser en production, nous devons augmenter la sécurité de celui ci et de notre système.

_________________________________________________________________________________

### Créer de nouveaux utilisateurs MariaDB

Une bonne pratique lors de l’utilisation de bases de données est de créer un nouvel utilisateur pour chaque application, avec des permissions se limitant à la base concernée.

N’utilisez jamais de compte administrateur dans vos applications, pour des raisons de sécurité évidentes. Si une de vos applications étaient compromises, toutes vos bases le seraient également.

Voici la procédure à suivre pour chaque nouvelle application web utilisant une base de données :

1.Créer un nouvel utilisateur

2.Créer une nouvelle base

3.Accorder au nouvel utilisateur des permissions à la nouvelle base

4.Recharger les privilèges

```bash
sudo mysql -u root -p
CREATE USER 'newuser'@'localhost' IDENTIFIED BY 'password';
CREATE DATABASE newbase;
GRANT ALL PRIVILEGES ON newbase.* TO 'newuser'@'localhost';
FLUSH PRIVILEGES;
QUIT;
``` 

### Définir les permissions des fichiers et dossiers

Le mieux pour gérer ses autorisations de fichiers et de créer un nouvel utilisateur système pour chaque application et attribuer les droits du dossier publique du site web à celui ci.

Cela limite la portée de l’accès en cas d’intrusion frauduleuse.

Cet utilisateur aura un accès complet au site, et l’utilisateur www-data de Nginx n’aura que des droits d’exécution :

```bash
sudo adduser sysuser
sudo chown sysuser:www-data -R /var/www/monsite/
sudo chmod -R 710 -R /var/www/monsite/
```

### Installation de phpmyadmin

```bash
sudo chmod 777 -R root
sudo cd /root/ && wget https://files.phpmyadmin.net/phpMyAdmin/5.1.1/phpMyAdmin-5.1.1-all-languages.zip
unzip phpMyAdmin-5.1.1-all-languages.zip
```

### installer unzip si besoin
 
```bash
sudo mv phpMyAdmin-5.1.1-all-languages /var/www/phpmyadmin
```

### Copie du fichier de config par défaut

```bash
sudo cp -pr /var/www/phpmyadmin/config.sample.inc.php /var/www/phpmyadmin/config.inc.php
```

### Edition du fichier et modification du secret pour génération de cookies :
 
```bash
sudo nano /var/www/phpmyadmin/config.inc.php
$cfg['blowfish_secret'] = 'longuechaineagenererauhasard'; /* YOU MUST FILL IN THIS FOR COOKIE AUTH! */
```
récupération blowfish_secret generator -> standingtech.com

### Création des tables initiales :
 
```bash
sudo mysql </var/www/phpmyadmin/sql/create_tables.sql -u root -p
```
+ Mot de passe SQL de root

___________________________________________________________________

### Virtualhost pour Phpmyadmin

On crée un virtualhost pour phpmyadmin

```bash
sudo nano /etc/nginx/sites-available/phpmyadmin
``` 
et on complète son contenu (en utilisant la valeur du sock pour PHP-FPM trouvé
dans /etc/php/7.4/fpm/pool.d/www.conf qui est la pool par défaut).
0n peut choisir le port (ici 8080) pour garder un vhost spécifique.

server {

   listen 8081;

   server_name pma.monsite;

   root /var/www/phpmyadmin;

   location / {

      index index.php;

    }

   location ~* ^.+.(jpg|jpeg|gif|css|png|js|ico|xml)$ {

      access_log off;

      expires 30d;

   }

 

   location ~ /\.ht {

      deny all;

   }

 

   location ~ /(libraries|setup/frames|setup/libs) {

      deny all;

      return 404;

   }

 

   location ~ \.php$ {

      include /etc/nginx/fastcgi_params;

      fastcgi_pass unix:/run/php/php7.4-fpm.sock;

      # fastcgi_pass 127.0.0.1:9000;

      fastcgi_index index.php;

      fastcgi_param SCRIPT_FILENAME /var/www/phpmyadmin$fastcgi_script_name;

   }

}

 

Puis on active ce virtualhost :

 ```bash
sudo ln -s /etc/nginx/sites-available/phpmyadmin /etc/nginx/sites-enabled/
```

On crée un dossier temporaire avec les droits associés (!!! Bizarre comme méthode ???)

```bash
sudo mkdir /var/www/phpmyadmin/tmp
sudo chmod 660 /var/www/phpmyadmin/tmp
```

On autorise www:data (utilisateur de nginx) à accéder au dossier :

```bash
sudo chown -R www-data:www-data /var/www/phpmyadmin
``` 

Et on redémarre les services :

```bash
sudo systemctl restart php7.4-fpm
sudo systemctl restart nginx
```

On n'oublie pas d'ouvrir les ports 8080 :

```bash
sudo ufw status # pour voir les ports ouverts
sudo apt-get install ufw # si pas installé
sudo ufw allow 8081 (permet de redemarré phpmyadmin)
``` 

Droit phpmyadmin

```bash
sudo chmod -R 755 var/www/phpmyadmin
```
___________________________________________________________

### Installation de git

```bash
sudo apt install git-all
```
Si vous utilisez une distribution basée sur Debian (Debian/Ubuntu/dérivés d’Ubuntu),
vous avez aussi besoin du paquet install-info :

 
```bash
sudo apt-get install install-info
```
### Configurer git

```bash
git config --list --show-origin
git config --global user.name "John Doe"
git config --global user.email johndoe@example.com
``` 

Faire un repository sur git

```bash
echo "# projet NGINX" >> README.md
git int
git add README.md
git commit -m "first commit"
git branch -M master
git remote add origin htpps://github.com/davgui35/projetNGINX.git
git push -u origin master
```

### Configuration ssh key pour le dossier git

```bash
sudo su
ssh-keygen -t rsa
```

_Vérification de l'ajout ssh

```bash
ls -l ~/.ssh
```

Aller dans settings/developper setting/Personal Access Token => generate New token =>
erztrtrtertretretr8478ret48rette

Correspond au password push git

### Installation de JAVA

```bash
sudo apt update
sudo apt install openjdk-11-jdk
java -version
```

### Jenkins

L'intégration continue, qu'est-ce que c'est ?
Pour faire simple, le principe est de vérifier,
idéalement à chaque modification de code source,
que le résultat de ces modifications de produit pas de régression sur l'application.

Les principaux avantages de l'Intégration Continue sont les suivants :
Les problèmes d'intégration sont détectés rapidement, et peuvent donc être corrigés
au fil de l'eau, sans avoir à attendre une passe d'intégration manuelle qui n'a
lieu que trop rarement[2],
Les tests automatisés mis en place sur l'application, et joués à chaque intégration,
permettent d'identifier rapidement les changements problématiques,
La dernière version stable de l'application est connue,
et peut rapidement être obtenue (pour tests, démonstration, ...)

### Installation

```bash
wget -q -O - http://pkg.jenkins-ci.org/debian/jenkins-ci.org.key | sudo apt-key add -
sudo sh -c 'echo deb http://pkg.jenkins-ci.org/debian binary/ > /etc/apt/sources.list.d/jenkins.list'
sudo apt-get update
sudo apt-get install jenkins
```

### Mise en place


Configurer systeme => message Hello
configurer securité globale mettre html au lien de textplain
Mettre url git dans le nouveau job dans gestion code source

### trigger git => permet de vérifier l'état du git

credentials au cas où le git est private
Allez dans ce qui déclenche le build
Scrutation de l'outil de gestion de version
* * * * * => pour chaque minute (H) devant pour heures

Déclenchement de build automatique lors du changement de git


### Git push automatique

Définir une crédential = gestion de code source dans git Credentials=> Ajouter => jenkins
Mettre mon utilisateur nom git (davgui35) un mot de passe git(deploy key) et un ID

2.Avancé => Name = MonRepo
3.Build => executer un script shell

```bash
git config --global user.name "John Doe"
git config --global user.email johndoe@example.com
```
4.Actions à la suite du build
x Push Only if build Succeeds
Add tags => JENKINS-$BUILD_ID

tag message => Jenkins build
x create new tag
x Update new tag

Target remote name MonRepo


Création de tags Jenkins-.... dans github en cliquant branch master tag

### sonarqube (pipeline)

1.installer sonarqube Scanner plugin

### Installer sonarqube

```bash
sudo nano /etc/sysctl.conf
```
vm.max_map_count=262144
fs.file-max=65536
ulimit -n 65536
ulimit -u 4096

```bash
 sudo nano /etc/security/limits.conf
``` 

Scroll to the bottom of this file and paste the following:

sonarqube   -   nofile   65536
sonarqube   -   nproc    4096

```bash
sudo reboot
wget -q https://www.postgresql.org/media/keys/ACCC4CF8.asc -O - | sudo apt-key add -
sudo sh -c 'echo "deb http://apt.postgresql.org/pub/repos/apt/ lsb_release -cs-pgdg main" >> /etc/apt/sources.list.d/pgdg.list'
sudo apt-get update
```

### Install PostgreSQL

```bash
sudo apt install postgresql postgresql-contrib -y
sudo systemctl start postgresql
sudo systemctl enable  postgresql
sudo passwd postgres (root)
su - postgres
createuser sonar
psql

```

->ALTER USER sonar WITH ENCRYPTED PASSWORD 'root';
->CREATE DATABASE sonarqube OWNER sonar;
->GRANT ALL PRIVILEGES ON DATABASE sonarqube to sonar;
->\q

2.Télecharger sonarqube

```bash
wget https://binaries.sonarsource.com/Distribution/sonarqube/sonarqube-8.6.1.40680.zip
sudo apt-get install zip -y
unzip sonarqube*.zip
sudo mv sonarqube-XXX /opt/sonarqube 
```

### Configurer sonarqube

```bash
sudo nano /opt/sonarqube/conf/sonar.properties
```
sonar.projectKey=sonarqube
sonar.jdbc.username=sonar
sonar.jdbc.password=root
sonar.jdbc.url=jdbc:postgresql://localhost/sonarqube
sonar.search.javaOpts=-Xmx512m -Xms512m -XX:MaxDirectMemorySize=256m -XX:+HeapDumpOnOutOfMemoryError
sonar.web.host=localhost
sonar.web.port=9000
sonar.web.javaAdditionalOpts=-server
sonar.search.javaOpts=-Xmx512m -Xms512m -XX:+HeapDumpOnOutOfMemoryError
sonar.log.level=INFO
sonar.path.logs=logs

```bash
sudo nano /opt/sonarqube/bin/linux-x86-64/sonar.sh
```
->RUN_AS_USER=sonar


```bash
sudo nano /etc/systemd/system/sonarqube.service
```

[Unit]

Description=SonarQube service

After=network.target network-online.target

[Service]

Type=forking

ExecStart=/opt/sonarqube/bin/linux-x86-64/sonar.sh start

ExecStop=/opt/sonarqube/bin/linux-x86-64/sonar.sh stop

User=sonar

Group=sonar

LimitNOFILE=65536

LimitNPROC=4096

[Install]

WantedBy=multi-user.target

 
```bash
sudo systemctl start sonarqube
sudo systemctl enable sonarqube
sudo nano /etc/nginx/sites-enabled/sonarqube.conf
```

server{

    listen      80;

    server_name sonarqube.da.com;

    access_log  /var/log/nginx/sonar.access.log;

    error_log   /var/log/nginx/sonar.error.log;

    proxy_buffers 16 64k;

    proxy_buffer_size 128k;

    location / {

        proxy_pass  http://127.0.0.1:9000;

        proxy_next_upstream error timeout invalid_header http_500 http_502 http_503 http_504;

        proxy_redirect off;

        proxy_set_header    Host            $host;

        proxy_set_header    X-Real-IP       $remote_addr;

        proxy_set_header    X-Forwarded-For $proxy_add_x_forwarded_for;

        proxy_set_header    X-Forwarded-Proto http;

    }

}



```bash
sudo systemctl restart nginx
```

3 générate token for jenkin pour s'authentifier

configuration system jenkins -> sonarqube servers =>

nom sonarqube

url serveur => http://localhost:9000

Dans sonarqube => My Account => security => generate token en entrant le nom jenkin-pipeline

Dans jenkins => add credantials jenkins => secret Text


4.ajouter sonar-scanner dans jenkins

_sonar-scanner cli_

```bash
sudo mkdir downloads
sudo chmod 777 -R downloads
cd downloads
wget https://binaries.sonarsource.com/Distribution/sonar-scanner-cli/sonar-scanner-cli-4.6.2.2472-linux.zip
unzip sonar-scanner-cli-4.6.2.2472-linux.zip
mv sonar-scanner-4.6.2.2472-linux/ sonar-scanner
sudo cp -rv sonar-scanner /opt/sonarqube/
```

5. Change sonar-scanner.properties

Properties:

/opt/sonarqube/sonar-scanner/conf =>

```bash
sudo nano sonar-scanner.properties
```
Mettre une variable environnement sonar-scanner

```bash
sudo nano /etc/profile.d/scanner.sh
export PATH=$PATH:/opt/sonarqube/sonar-scanner/bin/
source /etc/profile.d/scanner.sh
```

Confirmer la variable d'environnement

```bash
which sonar-scanner
/opt/sonarqube/sonar-scanner/bin/sonar-scanner
``` 

6. Creer un projet dans sonarqube

-> generate un token

-> plugin sonarqube-scanner

-> configure sonarqube avec token

-> install sonar-scanner


```bash
start sonarqube -> sudo su -c '/opt/sonarqube/bin/linux-x86-64/sonar.sh start' sonar

``` 

### Install maven

```bash
cd /home
wget https://sonarsource.bintray.com/Distribution/sonarqube/sonarqube-5.6.6.zip
unzip sonarqube-5.6.6.zip
sudo mv sonarqube-5.6.6 /etc/sonarqube
cd  /etc/sonarqube/bin/linux-x86-64
./sonar.sh start
sudo apt-get install maven
cd /usr/share/maven/conf
sudo nano settings.xml
```

Aller à la balise pluginGroups et ajouter le plugin suivant:


<pluginGroup>org.sonarsource.scanner.maven</pluginGroup>

Aller à la balise profiles et ajouter ce code

<profile>

   <id>sonar</id>

    <activation>

        <activeByDefault>true</activeByDefault>

    </activation>

     <properties>

        <sonar.host.url>http://localhost:9000

     </sonar.host.url>

     </properties>

</profile>

 
### Test avec sonarqubes

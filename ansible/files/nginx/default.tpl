server {
    listen 80;
    root {{ doc_root }};
    index index.html index.php;

    server_name {{ server_name }};

    location / {
        try_files $uri $uri/ /index.php$is_args$args;
    }

    location ~ ^/(css|fonts|img|images|lib|js)/([^\/]+)/(.*)$ {
        try_files $uri $uri/ /proxy.php?module=$2&type=$1&file=$3;
    }

    location ~ \.php$ {
        fastcgi_pass   unix:/var/run/php5-fpm.sock;
        fastcgi_param  SCRIPT_FILENAME $document_root$fastcgi_script_name;
        fastcgi_param  APPLICATION_ENV development;
        include        fastcgi_params;
    }

    location ^.ht* {
        deny all;
    }
}
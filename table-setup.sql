create table video_inventory (
    id int auto_increment primary key,
     name varchar(255) unique not null,
     category varchar(255),
     length int(11) unsigned,
     rented bool not null default 1
     );

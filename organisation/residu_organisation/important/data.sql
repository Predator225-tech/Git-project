create table media(
    id int  auto_increment primary key,
    id_utilisateurs int not null, 
    chemin varchar(525),
    mediatype ENUM('photo', 'video') NOT NULL,
    titre varchar(123),
    descriptif varchar(800),
    publication datetime default current_timestamp,
    foreign key (id_utilisateurs) references utilisateur(id_utilisateur)
);

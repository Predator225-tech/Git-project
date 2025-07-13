/*commande d'affichage des départements ainsi que les noms des manager en cours et des nombres des employees de chaque départements*/
select e.emp_no,e.birth_date,e.last_name,e.first_name,e.hire_date,dm.dept_no,dm.from_date,dm.to_date,d.dept_name
    from employees e join dept_manager dm on e.emp_no=dm.emp_no
    join departments d on dm.dept_no=d.dept_no;

create or replace view v_employees_deptmanage_departments as 
select e.emp_no,e.birth_date,e.last_name,e.first_name,e.hire_date,dm.dept_no,dm.from_date,dm.to_date,d.dept_name
    from employees e join dept_manager dm on e.emp_no=dm.emp_no
    join departments d on dm.dept_no=d.dept_no;
select * from v_employees_deptmanage_departments where to_date='9999-01-01';


/*commande d'affichage des employé(e)s en fonction de l'indice recupèré PS: ce sont les dept_no CHAR();*/
SELECT e.emp_no,e.last_name,e.first_name,e.gender,de.dept_no,d.dept_name from employees e 
    join dept_emp de on de.emp_no=e.emp_no
    join departments d on d.dept_no=de.dept_no;
    
create or replace view v_employees_deptemp_departements as 
SELECT e.emp_no,e.last_name,e.first_name,e.gender,de.dept_no,d.dept_name from employees e 
    join dept_emp de on de.emp_no=e.emp_no
    join departments d on d.dept_no=de.dept_no;
select * from v_employees_deptemp_departements limit 0,20;



/*commande de count des employé(e)s pour chaque département*/
SELECT  count(*) as nombre_employé from v_employees_deptemp_departements where dept_no='d001';




/*commande affichage des fiche employées*/
create or replace view v_all as

---test
/*
select e.emp_no,e.last_name,e.first_name,e.hire_date as embauche,de.from_date as datefrom_dept_emp,NULLIF(de.to_date, '9999-01-01') as dateto_dept_emp,sa.from_date as safrom_date,
NULLIF(sa.to_date, '9999-01-01') AS sato_date,sa.salary as salaire
    from employees e join dept_emp de on e.emp_no=de.emp_no
    join departments d on de.dept_no=d.dept_no
    join salaries sa on sa.emp_no=e.emp_no 
    join titles ti on ti.emp_no=e.emp_no
    where e.emp_no=10017 group by sa.to_date;

    ,ti.from_date as tifrom_date,NULLIF(ti.to_date, '9999-01-01') AS tito_date,ti.title as fonction
    left join dept_manager dm on dm.dept_no=d.dept_no limit 0,20;
    
*/
---test
create or replace view v_information as 
select e.emp_no,e.last_name,e.first_name,e.gender,d.dept_name,d.dept_no,e.hire_date as embauche,
de.from_date as datefrom_dept_emp,NULLIF(de.to_date, '9999-01-01') as dateto_dept_emp,sa.from_date as safrom_date,
NULLIF(sa.to_date, '9999-01-01') AS sato_date,sa.salary as salaire
    from employees e join dept_emp de on e.emp_no=de.emp_no
    join departments d on de.dept_no=d.dept_no
    join salaries sa on sa.emp_no=e.emp_no;

SELECT * from v_information where emp_no=10295 group by sato_date;



/*requete pour voir les nombre d'employer par genre*/
select e.emp_no,e.last_name,e.first_name,e.gender,e.hire_date,d.dept_name,d.dept_no from employees e 
    join dept_emp de on e.emp_no=de.emp_no
    join departments d on d.dept_no=de.dept_no; 

create or replace view v_ingo_genre as 
select e.emp_no,e.last_name,e.first_name,e.gender,e.hire_date,d.dept_name,d.dept_no from employees e 
    join dept_emp de on e.emp_no=de.emp_no
    join departments d on d.dept_no=de.dept_no;

select * from v_ingo_genre where gender='M' and dept_no='d009';


/*requete pour calcul moyenne  salaire*/
select d.dept_name,de.dept_no,sa.salary,avg(sa.salary) as salaire_moyen from departments d 
    join dept_emp de on de.dept_no=d.dept_no
    join salaries sa on sa.emp_no=de.emp_no where d.dept_no='d002';


SELECT AVG(s.salary) as salaire_moyen FROM salaries s
        JOIN dept_emp dep ON dep.emp_no = s.emp_no
        join employees e on e.emp_no=s.emp_no
        WHERE dep.dept_no = 'd002' and e.gender='%s';

  
/*requete prendre historique des emploies*/
create or replace view v_info_titre as 
select e.emp_no,de.dept_no,d.dept_name,ti.from_date as début,NULLIF(ti.to_date,'9999-01-01') as fin,ti.title from employees e 
    join dept_emp de on de.emp_no=e.emp_no
    join departments d on d.dept_no =de.dept_no
    join titles ti on ti.emp_no=e.emp_no;
    
    where e.emp_no=10009
    group by ti.title;


/*requete le temps le plus long en fonction du departement*/

--test update;
create table test(
    emp_no int ,
    from_date date ,
    departement varchar(20)
);
insert into test(emp_no,from_date,departement) values(1002,'2011-03-01','marketingfake');
create or replace view v_test as 
select * from test;
update test set from_date="2013-01-23", departement='devfake' where emp_no=1002; 
drop table test;
---test:succès retentissant

/*requete pour modification/transfert departement*/
update dept_emp set to_date=%d where emp_no=%d
insert into dept_emp(emp_no)


/*test chager departement parametre de test*
paramètre : cobaye emp_no 10295,nom+prenom:Kristine Velardi
MariaDB [employees]> SELECT * from v_information where emp_no=10295 group by sato_date;
+--------+-----------+------------+--------+-----------+---------+------------+-------------------+-----------------+-------------+------------+---------+
| emp_no | last_name | first_name | gender | dept_name | dept_no | embauche   | datefrom_dept_emp | dateto_dept_emp | safrom_date | sato_date  | salaire |
+--------+-----------+------------+--------+-----------+---------+------------+-------------------+-----------------+-------------+------------+---------+
|  10295 | Velardi   | Kristine   | M      | Finance   | d002    | 1990-08-27 | 1995-06-19        | 2025-07-24      | 2002-06-17  | NULL       |   52162 |
|  10295 | Velardi   | Kristine   | M      | Finance   | d002    | 1990-08-27 | 1995-06-19        | 2025-07-24      | 1995-06-19  | 1996-06-18 |   46398 |
|  10295 | Velardi   | Kristine   | M      | Finance   | d002    | 1990-08-27 | 1995-06-19        | 2025-07-24      | 1996-06-18  | 1997-06-18 |   47440 |
|  10295 | Velardi   | Kristine   | M      | Finance   | d002    | 1990-08-27 | 1995-06-19        | 2025-07-24      | 1997-06-18  | 1998-06-18 |   48556 |
|  10295 | Velardi   | Kristine   | M      | Finance   | d002    | 1990-08-27 | 1995-06-19        | 2025-07-24      | 1998-06-18  | 1999-06-18 |   49187 |
|  10295 | Velardi   | Kristine   | M      | Finance   | d002    | 1990-08-27 | 1995-06-19        | 2025-07-24      | 1999-06-18  | 2000-06-17 |   51375 |
|  10295 | Velardi   | Kristine   | M      | Finance   | d002    | 1990-08-27 | 1995-06-19        | 2025-07-24      | 2000-06-17  | 2001-06-17 |   52811 |
|  10295 | Velardi   | Kristine   | M      | Finance   | d002    | 1990-08-27 | 1995-06-19        | 2025-07-24      | 2001-06-17  | 2002-06-17 |   52346 |
+--------+-----------+------------+--------+-----------+---------+------------+-------------------+-----------------+-------------+------------+---------+
8 rows in set (0,001 sec)

MariaDB [employees]> select * from dept_emp where emp_no=10295;
+--------+---------+------------+------------+
| emp_no | dept_no | from_date  | to_date    |
+--------+---------+------------+------------+
|  10295 | d002    | 1995-06-19 | 2025-07-24 |
|  10295 | d004    | 2025-07-25 | 2025-07-27 |
|  10295 | d006    | 2025-07-24 | 2025-07-25 |
+--------+---------+------------+------------+
3 rows in set (0,001 sec)

MariaDB [employees]> select * from dept_emp where emp_no=10295;
+--------+---------+------------+------------+
| emp_no | dept_no | from_date  | to_date    |
+--------+---------+------------+------------+
|  10295 | d002    | 1995-06-19 | 2025-07-24 |
|  10295 | d004    | 2025-07-25 | 2025-07-27 |
|  10295 | d006    | 2025-07-24 | 2025-07-25 |
+--------+---------+------------+------------+
3 rows in set (0,001 sec)*/
/*test de modification*/
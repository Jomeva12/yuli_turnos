el proyecto trata de una aplicación web. El problema actual que tiene la persona que me solicitó el proyecto, es que es la encargada de crear los turnos del personal que tiene a cargo, que son aproximadamente 30 personas. Debe crear los turnos para todo el mes.
Es un almacén tipo centro comercial.
En un mes, las personas deben descansar cuatro días, de esos cuatro dias 3 son en dias ordinarios y 1 en domingo. El descanso, la idea es que sea automático.

El usuario debe marcar las vacaciones, incapacidades.  Las vacaciones deben ser con un inicio y un final. Deben marcarse en todos los turnos del mes.
 
 Debemos contar con un seeder de todas las personas que van a estar en la base de datos.(ya contamos con esto, muy bien)
 En el centro comercial existen unas áreas. Todas las personas no están habilitadas para trabajar en esas áreas. Por ende, debemos contar con una configuración en donde seleccionemos a la  persona y le marquemos en check las áreas que tiene disponible. (ya contamos con esto, muy bien)

los días 28 al 2 de cada mes no se debe tener días de descanso y si es el caso debemos mostrarlo resaltado.

##deseos
IMPORTANTE: implementar sistema de notas sobre novedades en la generacion de los turnos para que el usuario pueda ver que paso con la generacion de los turnos, ejemplo: no se pudo generar el turno para el empleado X porque no se le asignaron las areas, o no se pudo generar el turno para el empleado Y porque tenia una vacacion y no se le asigno el turno, al empleado no se x no se le puede quedar debiuendo descanso y por ende se le asignó el dia x como descanso.

en el lugar de los botones de generar turno, generar rango y generar mes, implementar un boton para visualizar las novedades durante la generacion, mostremos tipo badge

=================================================================
##Lunes

- el área de electrodomésticos debe tener 2 personas: una en la mañana de (7:00 a 11:00-11:30 a 14:30), y (de 14:00 a 16:30- 17:00 a 21:30).
-domicilios 2 personas:  una persona en (7:00 a 11:00-11:30 a 14:30), y (de 14:00 a 16:30- 17:00 a 21:30).
-cosmeticos debe tener 2 personas: una en la mañana de (7:00 a 11:00-11:30 a 14:30), y (de 14:00 a 16:30- 17:00 a 21:30).
-buffet debe tener 1 personas: una en la mañana de (11:00 a 14:00-17:00 a 21:00)
total 7 personas
-general en la mañana 1 persona (7:00 a 11:00-11:30 a 14:30)
-general en la mañana 1 persona (7:00 a 11:30-12:00 a 14:30)
-general en la mañana 1 persona (8:00 a 12:00-12:30 a 15:30)
-general en la mañana 1 persona (8:30 a 11:30-16:00 a 20:00)
-general en la mañana 1 persona (9:00 a 13:00-13:30 a 16:30)
-general en la mañana 1 persona (10:00 a 13:00-16:30 a 21:00)
-general en la mañana 1 persona (10:30 a 13:30-14:00 a 17:30)
-general en la mañana 1 persona (11:00 a 13:00-13:30 a 18:30)
-general en la mañana 1 persona (11:30 a 14:00-14:30 a 19:00)
-general en la tarde 1 persona (12:00 a 14:00-14:30 a 19:30)
-general en la mañana 1 persona (13:00 a 14:30-15:00 a 20:30)
-general en la tarde 1 persona (14:00 a 15:30-16:00 a 21:30)
-general en la tarde 1 persona (14:00 a 16:00-16:30 a 21:30)
-general en la tarde 1 persona (14:00 a 16:30-17:00 a 21:30)
total 14 personas

2 vacaciones que los encontrarás en la programacion  4 descansos

comodin
-general en la tarde 1 persona (12:00 a 14:00-14:30 a 19:30)
-general en la tarde 1 persona (12:00 a 14:00-14:30 a 19:30)
-general en la mañana 1 persona (10:30 a 13:30-17:00 a 21:00)
=================================================================
##Martes

- el área de electrodomésticos debe tener 2 personas: una en la mañana de (7:00 a 11:00-11:30 a 14:30), y (de 14:00 a 16:30- 17:00 a 21:30).
-domicilios 2 personas:  una persona en (7:00 a 11:00-11:30 a 14:30), y (de 14:00 a 16:30- 17:00 a 21:30).
-cosmeticos debe tener 2 personas: una en la mañana de (7:00 a 11:00-11:30 a 14:30), y (de 14:00 a 16:30- 17:00 a 21:30).
-buffet debe tener 1 personas: una en la mañana de (11:00 a 14:00-17:00 a 21:00)
-Marking debe tener 1 persona: una en la mañana de (8:00 a 12:00-12:30 a 15:30)
total 8 personas
-general en la mañana 1 persona (7:00 a 11:00-11:30 a 14:30)
-general en la mañana 1 persona (7:00 a 11:30-12:00 a 14:30)
-general en la mañana 1 persona (8:00 a 12:00-12:30 a 15:30)
-general en la mañana 1 persona (8:30 a 11:30-16:00 a 20:00)
-general en la mañana 1 persona (9:00 a 13:00-13:30 a 16:30)
-general en la mañana 1 persona (10:00 a 13:00-16:30 a 21:00)
-general en la mañana 1 persona (10:30 a 13:30-14:00 a 17:30)
-general en la mañana 1 persona (11:00 a 13:00-13:30 a 18:30)
-general en la mañana 1 persona (11:30 a 14:00-14:30 a 19:00)
-general en la tarde 1 persona (12:00 a 14:00-14:30 a 19:30)
-general en la mañana 1 persona (13:00 a 14:30-15:00 a 20:30)
-general en la tarde 1 persona (14:00 a 15:30-16:00 a 21:30)
-general en la tarde 1 persona (14:00 a 16:00-16:30 a 21:30)
-general en la tarde 1 persona (14:00 a 16:30-17:00 a 21:30)
total 14 personas

2 vacaciones que los encontrarás en la programacion  3 descansos

comodin
-general en la tarde 1 persona (12:00 a 14:00-14:30 a 19:30)
-general en la tarde 1 persona (12:00 a 14:00-14:30 a 19:30)
-general en la mañana 1 persona (10:30 a 13:30-17:00 a 21:00)
=====================================================
##Los días miércoles
- el área de electrodomésticos debe tener 3 personas: una en la mañana de (7:00 a 13:00-13:30 a 15:00), (9:30 a 13:30-14:00 a 17:30) y (de 13:30 a 16:00- 16:30 a 21:30).
-varely camacho debe tener 1 personas:  (7:00 a 11:00-15:00 a 19:00)
-domicilios 2 personas:  una persona en (7:00 a 13:00-13:30 a 15:00) y otra persona en (13:30 a 16:00- 16:30 a 21:30)
-cosmeticos debe tener 2 personas: una en la mañana de (7:00 a 13:00-13:30 a 15:00) y otra persona en (13:30 a 16:00- 16:30 a 21:30)
-buffet debe tener 1 personas: una en la mañana de (11:00 a 14:00-17:00 a 21:30)
total 9 personas
-general en la mañana 1 persona (7:00 a 12:00-12:30 a 15:00)
-general en la mañana 1 persona (7:00 a 12:30-13:00 a 15:00)
-general en la mañana 1 persona (7:30 a 13:00-13:30 a 15:30)
-general en la mañana 1 persona (8:00 a 11:30-16:00 a 20:00)
-general en la mañana 1 persona (8:30 a 13:30-14:00 a 16:30)
-general en la mañana 1 persona (9:00 a 13:30-14:00 a 17:00)
-general en la mañana 1 persona (9:30 a 13:30-14:00 a 17:30)
-general en la mañana 1 persona (10:00 a 14:00-14:30 a 18:00)
-general en la mañana 1 persona (10:00 a 13:30-17:00 a 21:00)
-general en la mañana 1 persona (10:30 a 14:00-14:30 a 18:30)
-general en la mañana 1 persona (11:00 a 14:00-14:30 a 19:00)
-general en la mañana 1 persona (11:30 a 14:00-14:30 a 19:30)
-general en la tarde 1 persona (12:30 a 14:00-14:30 a 20:00)
-general en la tarde 1 persona (13:00 a 15:30-16:00 a 21:00)
-general en la tarde 1 persona (13:30 a 15:30-16:00 a 21:30)
-general en la tarde 1 persona (13:30 a 16:00-16:30 a 21:10)
-general en la tarde 1 persona (13:30 a 16:00-16:30 a 21:10)

comodin
-general en la mañana 1 persona (9:00 a 13:30-14:00 a 17:00)
-general en la tarde 1 persona (12:00 a 14:00-14:30 a 20:00)

total 17 personas

2 vacaciones que los encontrarás en la programacion  por lo general no se programa descanso en esta fecha, a un empleado no se le puede quedar debiendo descanso, en caso hipotetico que se deba programar se puede utilizar el comodin. 
==============================================================
##jueves

- el área de electrodomésticos debe tener 2 personas: una en la mañana de (7:00 a 11:00-11:30 a 14:30), y (de 14:00 a 16:30- 17:00 a 21:30).
-domicilios 2 personas:  una persona en (6:30 a 11:00-11:30 a 14:00), y (de 14:00 a 16:30- 17:00 a 21:30).
-cosmeticos debe tener 2 personas: una en la mañana de (7:00 a 11:00-11:30 a 14:30), y (de 14:00 a 16:30- 17:00 a 21:30).
-buffet debe tener 1 personas: una en la mañana de (11:00 a 14:00-17:00 a 21:00)
-Marking debe tener 1 persona: una en la mañana de (8:00 a 12:00-12:30 a 15:30)
total 8 personas
-general en la mañana 1 persona (7:00 a 11:00-11:30 a 14:30)
-general en la mañana 1 persona (7:00 a 11:30-12:00 a 14:30)
-general en la mañana 1 persona (8:00 a 12:00-12:30 a 15:30)
-general en la mañana 1 persona (8:30 a 11:30-16:00 a 20:00)
-general en la mañana 1 persona (9:00 a 13:00-13:30 a 16:30)
-general en la mañana 1 persona (10:00 a 13:00-16:30 a 21:00)
-general en la mañana 1 persona (10:30 a 13:30-14:00 a 17:30)
-general en la mañana 1 persona (11:00 a 13:00-13:30 a 18:30)
-general en la mañana 1 persona (11:30 a 14:00-14:30 a 19:00)
-general en la tarde 1 persona (12:00 a 14:00-14:30 a 19:30)
-general en la tarde 1 persona (12:00 a 14:00-14:30 a 19:30)
-general en la mañana 1 persona (13:00 a 14:30-15:00 a 20:30)
-general en la tarde 1 persona (14:00 a 15:30-16:00 a 21:30)
-general en la tarde 1 persona (14:00 a 16:00-16:30 a 21:30)
-general en la tarde 1 persona (14:00 a 16:30-17:00 a 21:30)
total 15 personas

2 vacaciones que los encontrarás en la programacion  3 descansos

comodin
-general en la tarde 1 persona (12:00 a 14:00-14:30 a 19:30)
-general en la mañana 1 persona (10:30 a 13:30-17:00 a 21:00)
=================================================================
##Viernes

- el área de electrodomésticos debe tener 2 personas: una en la mañana de (7:00 a 11:00-11:30 a 14:30), y (de 14:00 a 16:30- 17:00 a 21:30).
-domicilios 2 personas:  una persona en (7:00 a 11:00-11:30 a 14:30), y (de 14:00 a 16:30- 17:00 a 21:30).
-cosmeticos debe tener 2 personas: una en la mañana de (7:00 a 11:00-11:30 a 14:30), y (de 14:00 a 16:30- 17:00 a 21:30).
-buffet debe tener 1 personas: una en la mañana de (11:00 a 14:00-17:00 a 21:00)
total 7 personas
-general en la mañana 1 persona (7:00 a 11:00-11:30 a 14:30)
-general en la mañana 1 persona (7:00 a 11:30-12:00 a 14:30)
-general en la mañana 1 persona (8:00 a 12:00-12:30 a 15:30)
-general en la mañana 1 persona (8:30 a 11:30-16:00 a 20:00)
-general en la mañana 1 persona (9:00 a 13:00-13:30 a 16:30)
-general en la mañana 1 persona (10:00 a 13:00-16:30 a 21:00)
-general en la mañana 1 persona (10:30 a 13:30-14:00 a 17:30)
-general en la mañana 1 persona (11:00 a 13:00-13:30 a 18:30)
-general en la mañana 1 persona (11:30 a 14:00-14:30 a 19:00)
-general en la tarde 1 persona (12:00 a 14:00-14:30 a 19:30)
-general en la mañana 1 persona (13:00 a 14:30-15:00 a 20:30)
-general en la tarde 1 persona (14:00 a 15:30-16:00 a 21:30)
-general en la tarde 1 persona (14:00 a 16:00-16:30 a 21:30)
-general en la tarde 1 persona (14:00 a 16:30-17:00 a 21:30)
total 14 personas

2 vacaciones que los encontrarás en la programacion  4 descansos

comodin
-general en la tarde 1 persona (12:00 a 14:00-14:30 a 19:30)
-general en la tarde 1 persona (12:00 a 14:00-14:30 a 19:30)
-general en la mañana 1 persona (10:30 a 13:30-17:00 a 21:00)
=================================================================


##Los días Sabados
- el área de electrodomésticos debe tener 3 personas: una en la mañana de (7:00 a 13:00-13:30 a 15:30), (9:30 a 13:30-14:00 a 18:00) y (de 13:00 a 16:00- 16:30 a 21:30).
-domicilios 2 personas:  una persona en (7:00 a 13:00-13:30 a 15:30) y otra persona en (13:00 a 16:00- 16:30 a 21:30)
-cosmeticos debe tener 2 personas: una en la mañana de (7:00 a 13:00-13:30 a 15:30) y otra persona en (13:00 a 16:00- 16:30 a 21:30)
-buffet debe tener 1 personas: una en la mañana de (11:00 a 14:00-17:00 a 21:30)
total 8 personas
-general en la mañana 1 persona (7:00 a 12:00-12:30 a 15:30)
-general en la mañana 1 persona (7:00 a 12:30-13:00 a 15:30)
-general en la mañana 1 persona (7:30 a 13:00-13:30 a 16:00)
-general en la mañana 1 persona (8:00 a 12:00-16:00 a 20:00)
-general en la mañana 1 persona (8:30 a 13:30-14:00 a 17:00)
-general en la mañana 1 persona (9:00 a 13:30-14:00 a 18:00)
-general en la mañana 1 persona (9:30 a 13:30-14:00 a 18:30)
-general en la mañana 1 persona (9:30 a 13:30-17:00 a 21:00)
-general en la mañana 1 persona (10:00 a 14:00-14:30 a 18:30)
-general en la mañana 1 persona (10:00 a 13:30-17:00 a 21:00)
-general en la mañana 1 persona (10:30 a 14:00-14:30 a 19:00)
-general en la mañana 1 persona (11:00 a 14:00-14:30 a 19:30)
-general en la mañana 1 persona (11:30 a 14:00-14:30 a 20:00)
-general en la tarde 1 persona (12:30 a 14:00-14:30 a 21:00)
-general en la tarde 1 persona (13:00 a 15:30-16:00 a 21:30)
-general en la tarde 1 persona (13:15 a 15:30-16:00 a 21:45)
-general en la tarde 1 persona (13:15 a 15:30-16:00 a 21:45)
-general en la tarde 1 persona (13:15 a 15:30-16:00 a 21:45)
total 18 personas

comodin
-general en la mañana 1 persona (10:30 a 14:00-14:30 a 19:00)
-general en la tarde 1 persona (12:00 a 14:00-14:30 a 20:30)



2 vacaciones que los encontrarás en la programacion  nunca tendremos descanso los sabados
=================================================================


##Los días Domingo
- el área de electrodomésticos debe tener 2 personas: una en la mañana de (8:00 a 13:00-13:30 a 15:30), y (de 13:00 a 15:30- 16:00 a 20:30).
-domicilios 2 personas:  una persona en (8:00 a 13:00-13:30 a 15:30), y (de 13:00 a 15:30- 16:00 a 20:30).
-cosmeticos debe tener 2 personas: una en la mañana de (8:00 a 13:00-13:30 a 15:30), y (de 13:00 a 15:30- 16:00 a 20:30).
total 6 personas


-general en la mañana 1 persona (8:00 a 12:00-12:30 a 15:30)
-general en la mañana 1 persona (8:00 a 12:00-12:30 a 15:30)
-general en la mañana 1 persona (9:30 a 13:30-14:00 a 17:00)
-general en la mañana 1 persona (10:30 a 14:00-14:30 a 18:00)
-general en la tarde 1 persona (12:30 a 14:30-15:00 a 20:00)
-general en la tarde 1 persona (13:00 a 15:30-16:00 a 20:30)
-general en la tarde 1 persona (13:00 a 15:30-16:00 a 20:30)
-general en la tarde 1 persona (13:00 a 15:30-16:00 a 20:30)
total 8 personas


comodin
-general en la mañana 1 persona (11:30 a 14:00-14:30 a 19:00)
-general en la tarde 1 persona (12:30 a 14:30-15:00 a 20:00)
-general en la mañana 1 persona (10:00 a 14:00-14:30 a 17:30)
-general en la mañana 1 persona (11:00 a 14:30-15:00 a 18:30)


2 vacaciones que los encontrarás en la programacion  descansan 10 personas


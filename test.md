Test para prácticar con DDD y el framework

La app actual, https://github.com/bejaofit/Laravel-ddd-sample tiene un sistema de reservas muy simple.
Ahora mismo solo permite una reserva por mesa.Queremos cambiar el sistema para que permita más de una reserva por mesa (funcionamiento normal).

Para la práctica:

Requisito 1.
-------------
* vamos a suponer que las mesas no se pueden dividir, pero puedes reservar una mesa para 2 personas menos de su ocupación.
* Es decir, una de 4 vale para 2 personas. Una de 8 vale para 6.
* * Sólo dos turnos al día, comida y cena, sin controlar la hora.
Hay que hacer un endpoint al que se le pase el número de comensales, busque la mesa adecuada, mire si no hay una reserva para el mismo día (comida o cena).

Requisito 2.
------------
Queremos ofrecer un servicio adicional a nuestros clientes, al reservar la mesa, pueden aportar sus datos metabolicos, y le daremos un cálculo de las calorias que deben comer usando Harris Bendict.En el endpoint, opcionalmente podran pasar la info de edad, peso, etc.. de cada persona y el sistema le devolverá las kcal de cada uno.

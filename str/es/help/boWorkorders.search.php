<?php
include("helpCommon.php");

helpHeader();
?>
<H3>Busqueda de Work Orders</H3>
La pantalla de búsqueda de Work Order esta dividida en dos partes. La primera parte busca una Work Order especifica por su número y (opcionalmente) su secuencia. La segunda permite filtrar la petición seleccionando una ò mas opciones.
The work order search screen is divided into two parts.  The first part searches for a specific work order by its number and (optionally) sequence.  The second allows one or more options to be selected for filtering the query.
<BR>
<BR>
<H4>Buscar por n° ó secuencia de WO</H4>
Si sabe el numero de Work Order, puede introducirlo en la parte de arriba del formulario para ver los detalles de la Work Order. No tiene que introducir un número de secuencia para verla. DCL enseñará los detalles de la Work Order solo si existe una secuencia para ese número de Work Order. Si hay dos o mas secuencias, DCL enseñará una lista de resultados y le permitira moverse como si hubiera realizado una búsqueda.
If you know the work order number, you can enter it in the top of the form to display the detail of the work order.  You do not have to enter a sequence number for display.  DCL will display the work order detail if only one sequence exists for that work order number.  Otherwise, if two or more sequences are found, DCL will display a search result list and allow you to browse as if you had performed a search.
<BR>
<BR>
<H4>Busqueda por parametros</H4>
DCL también le permite realizar una búsqueda especificando uno o mas filtros para ordenar las WO. Para parametros de busqueda que usen cajas-lista, puedes seleccionar muliples entradas por las que buscar (p.e. si el estado es abierto, sin asignar, o pendiente).
DCL also allows you to perform a search by specifying one or more filters to query work orders by.  This interface is rather powerful even though it is not quite finished.  Note that for search parameters that use list boxes, you can select multiple entries to search for (i.e., if the status is open, unassigned, or deferred).
<H5>Personal</H5>
Las checkboxes le permiten buscar diferentes campos dentro de las WO para saber quien hizo que. Estas son:
Checkboxes allow you to search different fields within work orders to find out who did what.  They are:
<UL>
<LI><B>Responsable</B>&nbsp;-&nbsp;Marque esta opcion para que aparezcan las WO de las que son responsables el personal selleccionado.Check this box to narrow the work orders down to work orders the selected personnel are responsible for.</LI>
<LI><B>Abierto por</B>&nbsp;-&nbsp;Seleccione esta para obtener las WO abiertas por el personal seleccionado.Select this to get the work orders opened by the selected personnel.</LI>
<LI><B>Cerrado por</B>&nbsp;-&nbsp;Esta opcion permite buscar WO cerradas por el personal seleccionado.This option allows you to search for work orders closed by selected personnel.</LI>
</UL>
<H5>Tema, Prioridad, Gravedad, Sitio, Estado</H5>
Si quiere estrechar su búsqueda sobre alguno de estos campos, seleccione los elementos apropiados.
If you want to narrow your search down by any of these fields, just select the appropriate items.
<H5>Fechas</H5>
Puede restringir rangos de fechas de algunos campos entre fechas. Seleccione las checkboxes de los campos apropiados y ajuste los datos De: y Para: como sea necesario. Los pequeños iconos de calendario a la derecha de los campos de fecha activan un calendario en JavaScript, para no meter la fecha a mano.
You can restrict date ranges of certain fields between dates.  Select the checkbox(es) of the appropriate fields and adjust the From: and To: dates as necessary.  The small calendar icons to the right of the date fields will pop up a JavaScript calendar if you prefer to select your dates the GUI way.
<H5>Asunto, Notas, Descripción</H5>
Selecciona cualquier combinacion de los tres campos y escriba su texto de busqueda en el area debajo. Para que coincida algun campo durante la busqueda, debe contener la frase especificada <EM>exactamente como fue escrita</EM>. En el caso de algunos servidores SQL (como el PostgreSQL) debera asegurarse que las mayusculas/minusculas esten correctamente escritas. Otros (como el Microsoft SQL Server) normalmente son capaces de realizar busquedas sin fijarse en las mayusculas y minusculas.
Select any combination of the three fields and type your search text in the input area below.  Note that searches on these fields are currently running as &quot;phrase&quot;.  This means that in order for a field to match, it must contain the specified phrase <EM>exactly as typed</EM>.  In the cases of some SQL servers (such as PostgreSQL default install), you may even have to make sure your case is correct.  Others (i.e., Microsoft SQL Server) are usually set up to perform case insensitive queries.
<?php
helpFooter();
?>

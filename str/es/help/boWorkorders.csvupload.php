<?php
include("helpCommon.php");

helpHeader();
?>
<H3>Carga a CSV Work Order</H3>
Las WO pueden cargarse usando un fichero CSV (valores separados por coma) con formato adecuado. Este formato es una opcion de importacion/exportacion común para la mayoria de paquetes de software que trabajan con datos en diferentes formatos.<BR>
<BR>
<H4>Campos Requeridos</H4>
Los campos requeridos están en negrita:
<UL>
<LI><B>producto</B>&nbsp;-&nbsp;Nombre or ID numerico del producto.</LI>
<LI>account&nbsp;-&nbsp;Nombre o ID numerico del producto.</LI>
<LI><B>deadlineon</B></LI>
<LI>eststarton</LI>
<LI>estendon</LI>
<LI><B>horasest</B></LI>
<LI><B>prioridad</B>&nbsp;-&nbsp;Nombre o ID numerico de la prioridad.</LI>
<LI><B>severidad</B>&nbsp;-&nbsp;Nombre o ID numerico de la severidad.</LI>
<LI>usuario</LI>
<LI>telefono</LI>
<LI><B>resumen</B></LI>
<LI>notas</LI>
<LI><B>descripccion</B></LI>
<LI><B>responsable</B>&nbsp;-&nbsp;Nombre o ID numerico de la persona responsable.</LI>
<LI>revision</LI>
</UL>
Fíjese en que sus permisos puede que no le permitan especificar algunos de los campos listados aqui.
<H4>Formato Fichero</H4>
Debe ser un fichero de texto comma-delimited.La primera fila debe contener los nombres campo como listado encima.Loa ficheros datos y texto deben estar encerrados en quotes dobles ( &quot; ).  Los datos deben presentarse en el mismo formato esperado por DCL.
<?php
helpFooter();
?>

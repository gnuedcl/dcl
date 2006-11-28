<?php
include("helpCommon.php");

helpHeader();
?>
<H3>Work Order CSV Upload</H3>
Les appels peuvent être uploadés grâce à un fichier CSV correctement formaté (valeur séparées par des virgules).  Ce fichier est une option d'import/export commune pour la pluspart des logiciels traitant des données de différents formats.
<BR>
<BR>
<H4>Champs supportés Fields</H4>
Les champs obligatoires sont en gras:
<UL>
<LI><B>Produit</B>&nbsp;-&nbsp;Nom ou ID numérique du produit.</LI>
<LI>client&nbsp;-&nbsp;Nom ou ID numérique du client.</LI>
<LI><B>Date limite</B></LI>
<LI>Début estimé</LI>
<LI>Fin estimée</LI>
<LI><B>Heures estimées</B></LI>
<LI><B>priorité</B>&nbsp;-&nbsp;Nom ou ID numérique de la priorité.</LI>
<LI><B>sévérité</B>&nbsp;-&nbsp;Nom ou ID numérique de la sévérité.</LI>
<LI>contact</LI>
<LI>telephone contact</LI>
<LI><B>résumé</B></LI>
<LI>notes</LI>
<LI><B>description</B></LI>
<LI><B>responsible</B>&nbsp;-&nbsp;Name or numeric ID of personnel responsible.</LI>
<LI>revision</LI>
</UL>
Note that your permissions may not permit you to specify some of the fields that are listed here.
<H4>File Format</H4>
The file must be a comma-delimited text file.  The first row should contain the field names as listed above.  Date and text fields must be enclosed in double quotes ( &quot; ).  Dates should be submitted in the same format expected by DCL.
<?php
helpFooter();
?>

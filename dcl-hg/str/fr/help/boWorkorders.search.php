<?php
include("helpCommon.php");

helpHeader();
?>
<H3>Recherche des appels</H3>
L'écran de recherche des appels est divisé en deux parties. La première partie sert à la recherche d'un appel spécifique par son numéro et (optionnellement) sa séquence.  La seconde permet de sélectionner une ou plusieurs options pour filtrer la requête.
<BR>
<BR>
<H4>Recherche par No d'Appel/Seq</H4>
Si vous connaissez le numéro d'appel, vous pouvez l'entrer en haut de l'écran pour afficher le détail de l'appel. Vous n'avez pas à entrer un numéro de séquence pour l'affichage.  DCL affichera le détail de l'appel seulement si une séquence existe pour ce numéro d'appel. Autrement, si plusieurs séquences sont trouvées, DCL affichera une liste de résultat et vous permettra de les parcourir comme si vous aviez demandé une recherche.
<BR>
<BR>
<H4>Recherche Par Paramètres</H4>
DCL vous permet également d'effectuer une recherche en spécifiant un ou plusieurs filtres pour faire récupérer les appels ordonnées. Cet interface est assez puissante, même si elle n'est pas terminée.  Notez que pour les paramètres de recherche qui utilisent des zones de listes, vous pouvez sélectionner plusieurs valeurs pour la recherche (i.e., si un statut est ouvert, non assigné the status ou remis à plus tard.)
<H5>Personnel</H5>
Les cases à cocher vous permettent de rechercher différents champs concernant les appels à trouver et qui a fait quoi. Il y a:
<UL>
<LI><B>Responsable</B>&nbsp;-&nbsp;Cochez cette case pour réduire la liste de tous les appels à ceux dont les personnees choisies sont responsables.</LI>
<LI><B>Ouvert Par</B>&nbsp;-&nbsp;Selectionnez ceci pour avoir les appels ouvert par les personnes choisies.</LI>
<LI><B>Fermé Par</B>&nbsp;-&nbsp;Cette option vous permet de rechercher les appels fermés par les personnes choisies.</LI>
</UL>
<H5>Produit, Priorité, Sévérité, Clients, Statuts</H5>
Si vous voulez réduire votre recherche à un de ces champs, sélectionnez les éléments appropriés.
<H5>Dates</H5>
Vous pouvez réduire les périodes de certains champs entre des dates.  Sélectionner les cases à cocher des champs appropriés et ajustez les dates From:(Depuis) et To: (a) comme il est nécessaire. Les petits icones calendriers à la droite des champs date vont faire apparaitre un petit calendrier Javascript si vous préférez sélectionner vos date de manière graphique. 
<H5>Résumé, Notes, Description</H5>
Selectionnéz n'importe quelle combinaison des trois champs et tapez votre texte de recherche dans la zone de saisie en dessous. Notez que la recherche sur ces champs se fait sur la &quot;phrase&quot entière;.  Cela signifie que pour qu'un champ corresponde, il faut qu'il contienne la phrase spécifiée <EM>exactement telle qu'elle est tapée</EM>.  Avec certaines bases de données, (comme l'install par défaut de  PostgreSQL), vous devez même tenir compte des minuscules/majuscules.  D'autres (i.e., Microsoft SQL Server) sont généralement configurés pour faire des recherche sans tenir compte des majuscules/minuscules.
<?php
helpFooter();
?>

<?xml version="1.0" encoding="UTF-8"?>
<?mso-application progid="Word.Document"?>
<w:wordDocument
	xmlns:w="http://schemas.microsoft.com/office/word/2003/wordml"
	xmlns:o="urn:schemas-microsoft-com:office:office"
	xmlns:wx="http://schemas.microsoft.com/office/word/2003/auxHint"
	xml:space="preserve">{strip}
	<w:fonts>
		<w:defaultFonts w:ascii="Arial" w:fareast="Arial" w:h-ansi="Arial" w:cs="Arial" />
	</w:fonts>
	<w:styles>
		<w:style w:type="paragraph" w:default="on">
			<w:name w:val="Normal" />
			<w:sz w:val="16" />
		</w:style>
	</w:styles>
	<w:body>
		<w:p>
			<w:pPr>
				<w:jc w:val="center" />
			</w:pPr>
			<w:r>
				<w:rPr>
					<w:b />
					<w:sz w:val="24" />
				</w:rPr>
				<w:t>Activity for {$VAL_ACCOUNTNAME|escape:"utf8xml"}</w:t>
			</w:r>
		</w:p>
		<w:p>
			<w:pPr>
				<w:jc w:val="center" />
			</w:pPr>
			<w:r>
				<w:rPr>
					<w:sz w:val="20" />
				</w:rPr>
				<w:t>{$VAL_DATEBEGIN|escape:"utf8xml"} - {$VAL_DATEEND|escape:"utf8xml"}</w:t>
			</w:r>
		</w:p>
{foreach key=status item=wolist from=$VAL_WO}
		<w:p>
			<w:r>
				<w:rPr>
					<w:b />
					<w:sz w:val="20" />
				</w:rPr>
				<w:br />
				<w:t>{$status|escape:"utf8xml"} ({$wolist|@count} Items)</w:t>
			</w:r>
		</w:p>
		<w:tbl>
			<w:tr>
{foreach from=$VAL_HEADERS item=header}
				<w:tc><w:p><w:r><w:rPr><w:b /></w:rPr><w:t>{$header|escape:"utf8xml"}</w:t></w:r></w:p></w:tc>
{/foreach}
			</w:tr>
{foreach key=key item=item from=$wolist}
			<w:tr>
				<w:tc><w:tcPr><w:tcW w:w="1116" w:type="dxa" /></w:tcPr><w:p><w:r><w:t>{if $item.dcl_status_type == 2}{$item.closedon|escape:"utf8xml"}{else}{$item.createdon|escape:"utf8xml"}{/if}</w:t></w:r></w:p></w:tc>
				<w:tc><w:tcPr><w:tcW w:w="732" w:type="dxa" /></w:tcPr><w:p><w:r><w:t>{$item.jcn|escape:"utf8xml"}{if $item.seq > 1}-{$item.seq|escape:"utf8xml"}{/if}</w:t></w:r></w:p></w:tc>
				<w:tc><w:tcPr><w:tcW w:w="1416" w:type="dxa" /></w:tcPr><w:p><w:r><w:t>{$item.product|escape:"utf8xml"}</w:t></w:r></w:p></w:tc>
				<w:tc><w:tcPr><w:tcW w:w="1416" w:type="dxa" /></w:tcPr><w:p><w:r><w:t>{$item.type_name|escape:"utf8xml"}</w:t></w:r></w:p></w:tc>
				<w:tc><w:tcPr><w:tcW w:w="5320" w:type="dxa" /></w:tcPr><w:p><w:r><w:t>{$item.description|escape:"utf8xml"}</w:t></w:r></w:p></w:tc>
			</w:tr>
{/foreach}
		</w:tbl>
{/foreach}
	</w:body>
</w:wordDocument>{/strip}

<!--
This is MY FrameMaker like input file
-->
<frm>
	<template name="test.fmt" />
	<doc>
		<!--															-->
		<!-- backgroud which goes automatically into the given frame	-->
		<!--															-->
		<bgtext targetFlow="Header">
			<para paraFmt="pageHeader">
Header Zeile 1<tab/>rechtsbuendig 1<br/>
<tab/>rechtsbuendig 2<br/>
Heade Zeile 3
			</para>
		</bgtext>
		<bgtext targetFlow="Footer">
			<para paraFmt="pageFooter">
Geschaeftsfuehrer:1<tab/>Banken: 1<tab/><tab/><tab/>Handelsregister:<br/>
Rolf Hellmig<tab/>Deutsche Bank Bergneustadt<tab/>IBAN DE26384700240061290300<tab/>Swift Code: DEUTDEDB385<tab/>Amtsgericht Koeln<br/>
Michael Jung<tab/>Sparkasse GM-Bergneustadt<tab/>IBAN DE363845000000000104190<tab/>Swift Code: WELADED1GMB<tab/>HRB 39067<br/>
<tab/>Volksbank Oberberg<tab/>IBAN DE98384621352005234010<tab/>Swift Code: GENODED1WIL<tab/>UST-ID-Nr. De157251639<br/>
<tab/><tab/><tab/><tab/>Steuer Nr. 212/5754/0266<br/>
			</para>
		</bgtext>
		<bgtext targetFlow="ReturnAddr">
			<para paraFmt="returnAddr">
Hellmig EDV GmbH | Dreisbacher Str. 30 | 51674 Wiehl
			</para>
		</bgtext>
		<bgtext targetFlow="Reference">
			<para paraFmt="reference">
Servicebericht
			</para>
		</bgtext>
		<text targetFlow="RcvrAddr">
			<para paraFmt="rcvr">
Herrn<br/>
Lukas Pfenning<br/>
<br/>
Musterstr. 4712<br/>
<br/>
12345 Anywhere<br/>
			</para>
		</text>
		<text targetFlow="Info">
			<para paraFmt="info">
Kunden-Nr.<tab/>29006<br/>
Auftrags-Nr.<tab/>EJ1505121125(27211)<br/>
Bearbeiter<tab/>Jippa, Eddie<br/>
Datum<tab/>12.05.2015<br/>
ID<tab/>27211<br/>
			</para>
		</text>
		<text targetFlow="default">
			<para ref="default">
11111<br/>
Dies ist ein Fliesstext der als ganzer block an die addText Funktion uebergeben werden sollte.
			</para>
			<para ref="default">
Dies ist ein Fliesstext der als ganzer block an die addText Funktion uebergeben werden sollte.
			</para>
			<para ref="default">
Dies ist ein Fliesstext der als ganzer block an die addText Funktion uebergeben werden sollte.
			</para>
			<para ref="default">
11111s dskfjds fhjsdfl kjsdhfl kjsdhf lkjsdhf lksdjf lksadjf saldkj lkjfas dlkjfas ldkfs dlkjf
lsdkjf lksdjhfl ksdjhfl kjsdhfl ksdjhfl kjsdhflfjhsdlkjh klsjdhfl ksd
Dies ist ein Fliesstext der als ganzer block an die addText Funktion uebergeben werden sollte.
			</para>
			<table tableFormat="serviceInfo">
				<tr>
					<td>Service Nr.</td>
					<td>ksjfhsdk</td>
					<td>Startszeit</td>
					<td>12.05.2015 11:30</td>
				</tr>
				<tr>
					<td>Service Typ</td>
					<td>value 22</td>
					<td>Endzeit</td>
					<td>12.05.2015 12:45</td>
				</tr>
				<tr>
					<td>Kontakt</td>
					<td>Max Mustermann</td>
					<td>Dauer</td>
					<td>01:15</td>
				</tr>
			</table>
		</text>
		<text targetFlow="default">
			<para ref="default">
9999999
			</para>
		</text>
	</doc>
</frm>

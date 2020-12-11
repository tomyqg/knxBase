all:
	php genbookset.php
	cp pdf/* /srv/www/vhosts/modis-gmbh.eu/httpdocs/kataloge

labshop:
	./genkatR1A -n Labor-Shop -C "12%"

xml:
	make xml_es_cl
	make xml_de_de

xml_es_cl:
#	./genxmlR1A -n es_cl_Kat_1_Messtechnik -C "11:11" -l es -m cl
	./genxmlR1A -n es_cl_Kat_2_LaborShop_12_11_10 -C "12:11:10" -l es -m cl
	./genxmlR1A -n es_cl_Kat_2_LaborShop_12_11_10 -C "12:11:10" -l es -m cl
	./genxmlR1A -n es_cl_Kat_2_LaborShop_12_11_20 -C "12:11:20" -l es -m cl
	./genxmlR1A -n es_cl_Kat_2_LaborShop_12_11_30 -C "12:11:30" -l es -m cl
	./genxmlR1A -n es_cl_Kat_2_LaborShop_12_11_40 -C "12:11:40" -l es -m cl
	./genxmlR1A -n es_cl_Kat_2_LaborShop_12_12_10 -C "12:12:10" -l es -m cl
	./genxmlR1A -n es_cl_Kat_2_LaborShop_12_12_30 -C "12:12:30" -l es -m cl
	./genxmlR1A -n es_cl_Kat_2_LaborShop_12_12_35 -C "12:12:35" -l es -m cl
	./genxmlR1A -n es_cl_Kat_2_LaborShop_12_12_40 -C "12:12:40" -l es -m cl
#	./genxmlR1A -n es_cl_Kat_4_Chemikalien -C "13" -l es -m cl
#	./genxmlR1A -n es_cl_Kat_5_Biologie -C "21:11" -l es -m cl
#	./genxmlR1A -n es_cl_Kat_6_Chemie -C "21:31" -l es -m cl
#	./genxmlR1A -n es_cl_Kat_7_Physik -C "21:41" -l es -m cl

xml_de_de:
	./genxmlR1A -n de_de_Kat_1_Messtechnik_11_11_01 -C "11:11:01" -l de -m de
	./genxmlR1A -n de_de_Kat_1_Messtechnik_11_11_02 -C "11:11:02" -l de -m de
	./genxmlR1A -n de_de_Kat_1_Messtechnik_11_11_20 -C "11:11:20" -l de -m de
	./genxmlR1A -n de_de_Kat_1_Messtechnik_11_11_30 -C "11:11:30" -l de -m de
	./genxmlR1A -n de_de_Kat_1_Messtechnik_11_11_40 -C "11:11:40" -l de -m de
	./genxmlR1A -n de_de_Kat_1_Messtechnik_11_11_50 -C "11:11:50" -l de -m de
	./genxmlR1A -n de_de_Kat_2_LaborShop_12_11_10 -C "12:11:10" -l de -m de
	./genxmlR1A -n de_de_Kat_2_LaborShop_12_11_20 -C "12:11:20" -l de -m de
	./genxmlR1A -n de_de_Kat_2_LaborShop_12_11_30 -C "12:11:30" -l de -m de
	./genxmlR1A -n de_de_Kat_2_LaborShop_12_11_40 -C "12:11:40" -l de -m de
	./genxmlR1A -n de_de_Kat_2_LaborShop_12_12_10 -C "12:12:10" -l de -m de
	./genxmlR1A -n de_de_Kat_2_LaborShop_12_12_30 -C "12:12:30" -l de -m de
	./genxmlR1A -n de_de_Kat_2_LaborShop_12_12_35 -C "12:12:35" -l de -m de
	./genxmlR1A -n de_de_Kat_2_LaborShop_12_12_40 -C "12:12:40" -l de -m de
#	./genxmlR1A -n de_de_Kat_4_Chemikalien -C "13" -l de -m de
	./genxmlR1A -n de_de_Kat_4_Chemikalien_13_11_02 -C "13:11:02" -l de -m de
	./genxmlR1A -n de_de_Kat_4_Chemikalien_13_11_03 -C "13:11:03" -l de -m de
	./genxmlR1A -n de_de_Kat_4_Chemikalien_13_11_04 -C "13:11:04" -l de -m de
	./genxmlR1A -n de_de_Kat_5_Biologie_21_11 -C "21:11" -l de -m de
	./genxmlR1A -n de_de_Kat_6_Chemie_21_31 -C "21:31" -l de -m de
	./genxmlR1A -n de_de_Kat_7_Physik_21_51_10 -C "21:41:10" -l de -m de
	./genxmlR1A -n de_de_Kat_7_Physik_21_51_20 -C "21:41:20" -l de -m de
	./genxmlR1A -n de_de_Kat_7_Physik_21_51_30 -C "21:41:30" -l de -m de
	./genxmlR1A -n de_de_Kat_7_Physik_21_51_40 -C "21:41:40" -l de -m de
	./genxmlR1A -n de_de_Kat_7_Physik_21_51_50 -C "21:41:50" -l de -m de
	./genxmlR1A -n de_de_Kat_7_Physik_21_51_60 -C "21:41:60" -l de -m de
	./genxmlR1A -n de_de_Kat_7_Physik_21_51_70 -C "21:41:70" -l de -m de

labxml:
	./genxmlR1A -n Kat_2_LaborShop -C "12"

main:
	./genkat -n MODIS-Shop -C ""
	mv MODIS-Shop.pdf pdf
	cp pdf/MODIS-Shop.pdf /srv/www/vhosts/modis-gmbh.eu/httpdocs/kataloge

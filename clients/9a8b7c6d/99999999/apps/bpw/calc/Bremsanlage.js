			
				var ie5;//ob internetexplorer verwendet wird
				var nn6;//ob nn verwedet wird
				var menuWidth; //menü breite
				var menuHeight;//menü höhe
				var menuStatus;//ob menü 'an' ist oder nicht
			
			//initialisierung der ventil buttons und der achsen beschriftung
			//checken des browsers
			//menu größe festlegen
			function Bremsanlage_initialize()
			{	
				document.getElementById("table").innerHTML="";
				
				for(var i=1;i<=7;i++)
				{
					for(var j=1;j<=axe_count;j++)
					{
						
						if(i == 1)
						{
							document.getElementById("table").innerHTML+='<div style="top:'+(j*100+250)+'px;left:'+(i*100-50)+'px;position:absolute;z-index:200;" class="button" id="'+i+''+j+'"><font color="white">Achse '+j+'</font></div>';
						}
						else if(i == 2)
						{
							document.getElementById("table").innerHTML+='<div style="background-color:#CCCCCC;top:'+(j*100+250)+'px;left:'+(i*100-50)+'px;position:absolute;z-index:200;cursor:pointer;background-image:url(button_border_1.png);" name="n" class="button" id="'+i+''+j+'"><font color="black">Undefiniert</font></div>';
						}
						else
						{
							document.getElementById("table").innerHTML+='<div style="visibility:hidden;background-color:#CCCCCC;top:'+(j*100+250)+'px;left:'+(i*100-50)+'px;position:absolute;z-index:200;cursor:pointer;background-image:url(button_border_1.png);" name="n" class="button" id="'+i+''+j+'"><font color="black">Undefiniert</font></div>';
						}
					}
				}
				
				//RechtsClickMenü:
				// Nur für IE 5+ und NN 6+
				ie5=(document.getElementById && document.all && document.styleSheets)?1:0;
				nn6=(document.getElementById && !document.all)?1:0;
				
				// RechtsClickMenü initialisieren
				if (ie5 || nn6) 
				{
					menuWidth=122; 
					menuHeight=183;
					menuStatus=0;

					// Rechter Mausklick: Menü anzeigen, linker Mausklick: Menü verstecken
					//document.oncontextmenu=showMenu; //oncontextmenu geht nicht bei NN 6.01
					document.onmouseup=hideMenu;
					
				}
				//column_count = 7;

				//right_click_x = 0;
				//right_click_y = 0;
				
				
				//adde zu jedem ventil button die entsprechenden rightclick und click events
				for(var k=2;k<=7;k++)
				{
					for(var l=1;l<=axe_count;l++)
					{
						//alert(k+""+l);
						$('#'+k+''+l).mousedown(function(event)
						{
							//alert(k+""+l);
						
							if(event.which == 1){open_ventil_dialog($(this).attr('id'));/*alert("actual ventil id:"+actual_clicked_ventil+"k+l:"+k+''+l+"object id:"+$(this).attr('id'))*/;}
							if(event.which == 3 && $(this).attr('name') != 'n')
							{
								actual_clicked_ventil = $(this).attr('id');
								//showMenu(event);
								if(event.pageX>menuWidth+window.pageXOffset) xPos=event.pageX-menuWidth;
								else xPos=event.pageX;
								if(event.pageY>menuHeight+window.pageYOffset) yPos=event.pageY-menuHeight;
								else yPos=event.pageY;
						
								document.getElementById("menu").style.left=xPos -100;
								document.getElementById("menu").style.top=yPos;
								menuStatus=1;
						
								document.getElementById("menu").style.visibility = "visible";
							}
						});
						//alert(k+''+l);
						//alert('#'+k+''+l);
						//alert(k+''+l);
					}
				}
				
			}
			/*
			var column_count = 7;

			var right_click_x = 0;
			var ight_click_y = 0;
			*/
			//Diese drei Funktionen werden nicht mehr verwendet:
			/*
			function hidecell (id)
			{
				document.getElementById(id).style.visibility="hidden";
			}
			
			function addcolumn()
			{
				if(column_count < 7)
				{
					for(var i=1;i<=axe_count;i++)
					{
						document.getElementById((column_count+1)+''+i).style.visibility="visible";
					}
					
					column_count += 1;
				}
			}
			
			function delcolumn()
			{
				if(column_count > 2)
				{
					for(var i=1;i<=axe_count;i++)
					{
						document.getElementById(column_count+''+i).style.visibility="hidden";
					}
					
					column_count -= 1;
				}
			}
			*/
			
			// RechtsClickMenü anzeigen
			function showMenu(event) {
				/*
				if(ie5) 
				{
					//right_click_x = event.clientX;
					//right_click_y = event.clientY;
					
					if(event.clientX>menuWidth) xPos=event.clientX-menuWidth+document.body.scrollLeft;
					else xPos=event.clientX+document.body.scrollLeft;
					if (event.clientY>menuHeight) yPos=event.clientY-menuHeight+document.body.scrollTop;
					else yPos=event.clientY+document.body.scrollTop;
				}
				else 
				{
				*/
					//right_click_x = e.pageX;
					//right_click_y = e.pageY;
					
					if(event.pageX>menuWidth+window.pageXOffset) xPos=event.pageX-menuWidth;
					else xPos=event.pageX;
					if(event.pageY>menuHeight+window.pageYOffset) yPos=event.pageY-menuHeight;
					else yPos=event.pageY;
				//}
				document.getElementById("menu").style.left=xPos;
				document.getElementById("menu").style.top=yPos;
				menuStatus=1;
				
				//document.getElementById("menu").style.visibility = "hidden";
				/*
				for(var i=2;i<=7;i++)
				{
					for(var j=1;j<=axe_count;j++)
					{
						if(mouseCollide(right_click_x, right_click_y,(i*100-50),(j*100+250)  , 100, 100))
						{
							//if(document.getElementById(i+''+j).style.visibility == "visible")
							{
								document.getElementById("menu").style.visibility = "visible";
							}
							alert("collide with "+i+""+j);
						}
					}
				}
				*/
				return false;
			}

			// RechtsClickMenü verstecken
			function hideMenu(e) 
			{
				if (menuStatus==1 && ((ie5 && event.button==1) || (nn6 && e.which==1))) 
				{
				setTimeout("document.getElementById('menu').style.top=-250",250);
				menuStatus=0;
				document.getElementById("menu").style.visibility = "hidden";
				}
			}
			
			//wird auch nicht mehr benötigt:
			/*
			function mouseCollide(MposX, MposY, posX, posY, width, height)
			{
				if(MposX > posX && MposX < posX + width
					&& MposY > posY && MposY < posY + height)
					{return true;}
				else{return false;}
			}
			*/
			function connecting_with_lower_cell()
			{
				var i = parseInt(actual_clicked_ventil.substring(0,1));
				var j = parseInt(actual_clicked_ventil.substring(1,2));
				//alert("i:"+i+"j:"+j);
				
				if(j<axe_count)
				{
					switch(document.getElementById(i+''+j).getAttribute("name"))
					{
						case 'a':
						{
							if(document.getElementById(i+''+ (j+1)).getAttribute("name") == 'a' || document.getElementById(i+''+ (j+1)).getAttribute("name") == 'n')
							{
								if($('#'+i+''+(j+1)).css('visibility') == 'visible')
								{
									document.getElementById(i+''+(j+1)).style.backgroundColor = document.getElementById(i+''+j).style.backgroundColor ;
									document.getElementById(i+''+ j).style.backgroundImage = "url(button_border_3.png)";
									document.getElementById(i+''+ j).style.backgroundPosition = "0px 0px";
									document.getElementById(i+''+ j).setAttribute("name", 'u');
									document.getElementById(i+''+ (j+1)).style.backgroundImage = "url(button_border_3.png)";
									document.getElementById(i+''+ (j+1)).style.backgroundPosition = "0px -200px";
									document.getElementById(i+''+ (j+1)).setAttribute("name", 'l');
									document.getElementById(i+''+ (j+1)).innerHTML = '';
								
									document.getElementById((i+1)+''+ (j+1)).style.visibility = 'visible';
								}
							}
							else
							{
								document.getElementById(i+''+(j+1)).style.backgroundColor = document.getElementById(i+''+j).style.backgroundColor ;
								document.getElementById(i+''+(j+2)).style.backgroundColor = document.getElementById(i+''+j).style.backgroundColor ;
								document.getElementById(i+''+ j).style.backgroundImage = "url(button_border_3.png)";
								document.getElementById(i+''+ j).style.backgroundPosition = "0px 0px";
								document.getElementById(i+''+ j).setAttribute("name", 'u');
								document.getElementById(i+''+ (j+1)).style.backgroundImage = "url(button_border_3.png)";
								document.getElementById(i+''+ (j+1)).style.backgroundPosition = "0px -100px";
								document.getElementById(i+''+ (j+1)).setAttribute("name", 'm');
								document.getElementById(i+''+ (j+1)).innerHTML = '';
								
								document.getElementById((i+1)+''+ (j+1)).style.visibility = 'visible';
								document.getElementById((i+1)+''+ (j+2)).style.visibility = 'visible';
							}
						}break;
						case 'l':
						{
							if($('#'+i+''+(j+1)).attr('name') == 'a' || document.getElementById(i+''+ (j+1)).getAttribute("name") == 'n')
							{
								if($('#'+i+''+(j+1)).css('visibility') == 'visible')
								{
									document.getElementById(i+''+(j+1)).style.backgroundColor = document.getElementById(i+''+j).style.backgroundColor ;
									document.getElementById(i+''+ (j+1)).style.backgroundImage = "url(button_border_3.png)";
									document.getElementById(i+''+ (j+1)).style.backgroundPosition = "0px -200px";
									document.getElementById(i+''+ (j+1)).setAttribute("name", 'l');
									document.getElementById(i+''+ j).style.backgroundImage = "url(button_border_3.png)";
									document.getElementById(i+''+ j).style.backgroundPosition = "0px -100px";
									document.getElementById(i+''+ j).setAttribute("name", 'm');
									document.getElementById(i+''+ (j+1)).innerHTML = '';
								
									document.getElementById((i+1)+''+ (j+1)).style.visibility = 'visible';
								}
							}
							else
							{
								document.getElementById(i+''+ j).style.backgroundImage = "url(button_border_3.png)";
								document.getElementById(i+''+ j).style.backgroundPosition = "0px -100px";
								document.getElementById(i+''+ j).setAttribute("name", 'm');
								
								document.getElementById(i+''+ (j+1)).style.backgroundImage = "url(button_border_3.png)";
								document.getElementById(i+''+ (j+1)).style.backgroundPosition = "0px -100px";
								document.getElementById(i+''+ (j+1)).setAttribute("name", 'm');
								
								$('#'+i+''+(j+1)).html('');
							}
						}break;
						case 'u':
						case 'm':
						{
							var height = get_height_of_cell_from_top(i,j);
							
							if($('#'+i+''+(j+height+1)).attr('name') == 'a' || document.getElementById(i+''+ (j+height+1)).getAttribute("name") == 'n')
							{
								if($('#'+i+''+(j+height+1)).css('visibility') == 'visible')
								{
									$('#'+i+''+(j+height+1)).attr('name','l');
									document.getElementById(i+''+ (j+height+1)).style.backgroundImage = "url(button_border_3.png)";
									document.getElementById(i+''+ (j+height+1)).style.backgroundPosition = "0px -200px";
									document.getElementById(i+''+(j+height+1)).style.backgroundColor = document.getElementById(i+''+j).style.backgroundColor 
									$('#'+i+''+(j+height+1)).html('');
								
									$('#'+(i+1)+''+(j+height+1)).css("visibility", "visible");
									
									$('#'+i+''+(j+height)).attr('name','m');
									document.getElementById(i+''+ (j+height)).style.backgroundImage = "url(button_border_3.png)";
									document.getElementById(i+''+ (j+height)).style.backgroundPosition = "0px -100px";
								}
							}
							else
							{
								$('#'+i+''+(j+height+1)).attr('name','m');
								document.getElementById(i+''+ (j+height+1)).style.backgroundImage = "url(button_border_3.png)";
								document.getElementById(i+''+ (j+height+1)).style.backgroundPosition = "0px -100px";
								document.getElementById(i+''+(j+height+1)).style.backgroundColor = document.getElementById(i+''+j).style.backgroundColor 
								$('#'+i+''+(j+height+1)).html('');
								
								$('#'+i+''+(j+height)).attr('name','m');
								document.getElementById(i+''+ (j+height)).style.backgroundImage = "url(button_border_3.png)";
								document.getElementById(i+''+ (j+height)).style.backgroundPosition = "0px -100px";
								/*
								var height_lower_cell = get_height_of_cell_from_top(i,j+height+1);
								
								for(var m = 0;m<= height_lower_cell;m++)
								{
									change background color(i,j+height+1+m);
								}
								*/
							}
						}break;
					}
				}
			}
			
			/*
			//diese funktion wird nicht verwendet, da sie im 'normalen' EG nicht verwendet wird
			//ACHTUNG: diese Funktion ist noch NICHT für die Verwendung bei einer beliebigen Achsenanzahl angepasst
			function connecting_with_upper_cell()
			{
				var i = parseInt(actual_clicked_ventil.substring(0,1));
				var j = parseInt(actual_clicked_ventil.substring(1,2));
				
				if(j>1)
				{
					switch(document.getElementById(i+''+j).getAttribute("name"))
					{
						case 'a':
						{
							if(document.getElementById(i+''+ (j-1)).getAttribute("name") == 'a' || document.getElementById(i+''+ (j-1)).getAttribute("name") == 'n')
							{
								document.getElementById(i+''+j).style.backgroundColor = document.getElementById(i+''+(j-1)).style.backgroundColor ;
								document.getElementById(i+''+ (j-1)).style.backgroundImage = "url(button_border_3.png)";
								document.getElementById(i+''+ (j-1)).style.backgroundPosition = "0px 0px";
								document.getElementById(i+''+ (j-1)).setAttribute("name",'u');
								document.getElementById(i+''+ j).style.backgroundImage = "url(button_border_3.png)";
								document.getElementById(i+''+ j).style.backgroundPosition = "0px -200px";
								document.getElementById(i+''+ j).setAttribute("name", 'l');
								document.getElementById(i+''+ j).innerHTML = '';
							}
							else
							{
								document.getElementById(i+''+j).style.backgroundColor = document.getElementById(i+''+(j-2)).style.backgroundColor ;
								document.getElementById(i+''+ (j-1)).style.backgroundImage = "url(button_border_3.png)";
								document.getElementById(i+''+ (j-1)).style.backgroundPosition = "0px -100px";
								document.getElementById(i+''+ (j-1)).setAttribute("name",'m');
								document.getElementById(i+''+ j).style.backgroundImage = "url(button_border_3.png)";
								document.getElementById(i+''+ j).style.backgroundPosition = "0px -200px";
								document.getElementById(i+''+j).setAttribute("name", 'l');
								document.getElementById(i+''+ j).innerHTML = '';
							}
						}break;
						case 'u':
						{
							document.getElementById(i+''+(j-1)).style.backgroundColor = document.getElementById(i+''+j).style.backgroundColor ;
							document.getElementById(i+''+ (j-1)).style.backgroundImage = "url(button_border_3.png)";
							document.getElementById(i+''+ (j-1)).style.backgroundPosition = "0px 0px";
							document.getElementById(i+''+ (j-1)).setAttribute("name",'u');
							document.getElementById(i+''+ j).style.backgroundImage = "url(button_border_3.png)";
							document.getElementById(i+''+ j).style.backgroundPosition = "0px -100px";
							document.getElementById(i+''+j).setAttribute("name", 'm');
							document.getElementById(i+''+ j).innerHTML = '';
							document.getElementById(i+''+ (j+1)).innerHTML = '';
						}break;
						case 'l':
						{
							if(j==3)
							{
								document.getElementById(i+''+(j-2)).style.backgroundColor = document.getElementById(i+''+j).style.backgroundColor ;
								document.getElementById(i+''+ (j-1)).style.backgroundImage = "url(button_border_3.png)";
								document.getElementById(i+''+ (j-1)).style.backgroundPosition = "0px -100px";
								document.getElementById(i+''+ (j-1)).setAttribute("name", 'm');
								document.getElementById(i+''+ (j-2)).style.backgroundImage = "url(button_border_3.png)";
								document.getElementById(i+''+ (j-2)).style.backgroundPosition = "0px 000px";
								document.getElementById(i+''+ (j-2)).setAttribute("name", 'u');
								document.getElementById(i+''+ (j-1)).innerHTML = '';
								document.getElementById(i+''+ j).innerHTML = '';
							}
						}break;
					}
				}
			}
			*/
			
			function disconnect_cell()
			{
				var i = parseInt(actual_clicked_ventil.substring(0,1));
				var j = parseInt(actual_clicked_ventil.substring(1,2));

				if(document.getElementById(i+''+j).getAttribute("name") != 'a')
				{
					switch(document.getElementById(i+''+j).getAttribute("name"))
					{
						case 'u':
						{
							document.getElementById(i+''+ j).style.backgroundImage = "url(button_border_1.png)";
							document.getElementById(i+''+ j).style.backgroundPosition = "0px 0px";
							document.getElementById(i+''+ (j+1)).style.color = "#FFFFFF";
											
							if(document.getElementById(i+''+ (j+1)).getAttribute("name") == 'm')
							{
								document.getElementById(i+''+ (j+1)).style.backgroundImage = "url(button_border_3.png)";
								document.getElementById(i+''+ (j+1)).style.backgroundPosition = "0px 0px";
								document.getElementById(i+''+ (j+1)).setAttribute("name", 'u');
								
								$('#'+i+''+(j+1)).html($('#'+i+''+j).html());
							}
							else 
							{
								document.getElementById(i+''+ (j+1)).style.backgroundImage = "url(button_border_1.png)";
								document.getElementById(i+''+ (j+1)).style.backgroundPosition = "0px 0px";
								document.getElementById(i+''+ (j+1)).setAttribute("name", 'a');
								$('#'+i+''+(j+1)).html($('#'+i+''+j).html());
							}
						}break;
						case 'l':
						{		
							var height_to_titel = get_height_of_cell_from_bottom(i, j);
							$('#'+i+''+j).html($('#'+i+''+(j-height_to_titel)).html());
						
							if(document.getElementById(i+''+ (j-1)).getAttribute("name") == 'm')
							{
								document.getElementById(i+''+ (j-1)).style.backgroundImage = "url(button_border_3.png)";
								document.getElementById(i+''+ (j-1)).style.backgroundPosition = "0px -200px";
								document.getElementById(i+''+ (j-1)).setAttribute("name", 'l');
								
								$('#'+i+''+j).html($('#'+i+''+(j-2)).html());
							}
							else
							{
								document.getElementById(i+''+ (j-1)).style.backgroundImage = "url(button_border_1.png)";
								document.getElementById(i+''+ (j-1)).style.backgroundPosition = "0px 0px";
								document.getElementById(i+''+ (j-1)).setAttribute("name", 'a');
								
								$('#'+i+''+j).html($('#'+i+''+(j-1)).html());
							}
							
							document.getElementById(i+''+ j).style.backgroundImage = "url(button_border_1.png)";
							document.getElementById(i+''+ j).style.backgroundPosition = "0px 0px";
							document.getElementById(i+''+j).setAttribute("name", 'a');
							document.getElementById(i+''+ j).style.color = "#FFFFFF";
							
											
						}break;
						case 'm':
						{	
							var height_to_titel = get_height_of_cell_from_bottom(i, j);
							$('#'+i+''+j).html($('#'+i+''+(j-height_to_titel)).html());
							$('#'+i+''+(j+1)).html($('#'+i+''+(j-height_to_titel)).html());
						
							if($('#'+i+''+(j-1)).attr('name') == 'm')
							{
								document.getElementById(i+''+ (j-1)).style.backgroundImage = "url(button_border_3.png)";
								document.getElementById(i+''+ (j-1)).style.backgroundPosition = "0px -200px";
								document.getElementById(i+''+ (j-1)).setAttribute("name", 'l');
							}
							else
							{
								document.getElementById(i+''+ (j-1)).style.backgroundImage = "url(button_border_1.png)";
								document.getElementById(i+''+ (j-1)).style.backgroundPosition = "0px 0px";
								document.getElementById(i+''+ (j-1)).setAttribute("name", 'a');
							}
							
							if($('#'+i+''+(j+1)).attr('name') == 'm')
							{
								document.getElementById(i+''+ (j+1)).style.backgroundImage = "url(button_border_3.png)";
								document.getElementById(i+''+ (j+1)).style.backgroundPosition = "0px 0px";
								document.getElementById(i+''+ (j+1)).setAttribute("name", 'u');
							}
							else
							{
								document.getElementById(i+''+ (j+1)).style.backgroundImage = "url(button_border_1.png)";
								document.getElementById(i+''+ (j+1)).style.backgroundPosition = "0px 0px";
								document.getElementById(i+''+ (j+1)).setAttribute("name", 'a');
							}
							
							document.getElementById(i+''+ j).style.backgroundImage = "url(button_border_1.png)";
							document.getElementById(i+''+ j).style.backgroundPosition = "0px 0px";
							document.getElementById(i+''+j).setAttribute("name", 'a');
							document.getElementById(i+''+ j).style.color = "#FFFFFF";
							document.getElementById(i+''+ (j+1)).style.color = "#FFFFFF";
						}break;
					}
					$('#'+i+''+j).attr('name', 'a');
				}
			}
			//Lösche Zelle: disconnecte sie von ihrem verbund und setze sie dann auf standardwerte zurück
			function delete_cell()
			{
				disconnect_cell();
				
				$('#'+actual_clicked_ventil).html('Undefiniert');
				$('#'+actual_clicked_ventil).attr('name','n');
				$('#'+actual_clicked_ventil).css("background-color", '#CCCCCC');
				$('#'+actual_clicked_ventil).css("color", '#000000');
				if($('#'+(parseInt(actual_clicked_ventil)+10)).attr('name') == 'n')
				{ 
					$('#'+(parseInt(actual_clicked_ventil)+10)).css('visibility', 'hidden')
				}
				else
				{
					//alert("raised actual clicked with 10");
					if($('#'+(parseInt(actual_clicked_ventil)+10)).attr('name') != 'n' && $('#'+actual_clicked_ventil).css('visibility') != 'hidden')
					{
						//alert("next cell must be deleted");
						$('#'+actual_clicked_ventil).attr('name', 'a');
						//alert("previos cell get name = a");
						$('#'+actual_clicked_ventil).html($('#'+(parseInt(actual_clicked_ventil)+10)).html());
						//alert("previos cell get hmtl from next cell");
						$('#'+actual_clicked_ventil).css("background-color", $('#'+(parseInt(actual_clicked_ventil)+10)).css("background-color"));
						//alert("previos cell get bgcolor from next cell, beginn with delete next cell");
						$('#'+actual_clicked_ventil).css("color", 'white');
						actual_clicked_ventil = parseInt(actual_clicked_ventil) + 10;
						actual_clicked_ventil = actual_clicked_ventil + '';
						delete_cell();
					}
				}

			}
			//suche das ende der zelle unter der aktuellen position
			function get_height_of_cell_from_top(x, starty)
			{
				var height = 0;
				var not_end = true;
				
				while(not_end)
				{
					if($('#'+x+''+(starty+height)).attr('name') != 'l')
					{
						height++;
					}
					else
					{
						not_end = false;
					}
				}
					
				return height;
			}
			//suche den anfang der zelle über der aktuellen position
			function get_height_of_cell_from_bottom(x, starty)
			{
				var height = 0;
				var not_end = true;
				
				while(not_end)
				{
					if($('#'+x+''+(starty-height)).attr('name') != 'u')
					{
						height++;
					}
					else
					{
						not_end = false;
					}
				}
					
				return height;
			}
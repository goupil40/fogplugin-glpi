/** fogtransfert.css
**	Fonctions masquage JS pour Fogplugin
**/

function contenu_grey()
{
	div = document.getElementById('contenu_grey');
	if(div.style.display == 'none')
	{
		div.style.display = 'block';
	}
	else
	{
		div.style.display = 'none';
	}
}

function contenu_green()
{
	div = document.getElementById('contenu_green');
	if(div.style.display == 'none')
	{
		div.style.display = 'block';
	}
	else
	{
		div.style.display = 'none';
	}
}

function contenu_orange()
{
	div = document.getElementById('contenu_orange');
	if(div.style.display == 'none')
	{
		div.style.display = 'block';
	}
	else
	{
		div.style.display = 'none';
	}
}

function contenu_red()
{
	div = document.getElementById('contenu_red');
	if(div.style.display == 'none')
	{
		div.style.display = 'block';
	}
	else
	{
		div.style.display = 'none';
	}
}

function select_all() {
  var items=document.getElementsByName('checkbox[]');
        for(var i=0; i<items.length; i++){
                if(items[i].type=='checkbox')
                        items[i].checked=true;
        }
}

function unselect_all(){
        var items=document.getElementsByName('checkbox[]');
        for(var i=0; i<items.length; i++){
                if(items[i].type=='checkbox')
                        items[i].checked=false;
        }
}			
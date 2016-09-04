function iconClicked(elem) {
	snackbarbutton = document.querySelector('#snackbarbutton')
	progressbar = document.querySelector('#progressbar')
	snackbar = document.querySelector('#snackbar')

	progressbar.style.bottom = '0px';
	httpRequest = new XMLHttpRequest();

    httpRequest.open('POST', 'php/submit.php');
	params = 'what=add&uid=' + elem.dataset.uid + '&drank=' + elem.dataset.drank;
	httpRequest.setRequestHeader('Content-type','application/x-www-form-urlencoded');

    httpRequest.onreadystatechange = function() {
		if (httpRequest.readyState === XMLHttpRequest.DONE) {
			if (httpRequest.status === 200) {
				console.log(httpRequest.responseText)

				progressbar.style.bottom = '-10px';
				
				elem.setAttribute('data-badge', httpRequest.responseText);

				snackbarbutton.setAttribute('data-drank', elem.dataset.drank);
				snackbarbutton.setAttribute('data-uid', elem.dataset.uid);
				var data = {
					message: capitalizeFirstLetter(elem.dataset.drank) + ' voor ' + elem.dataset.naam + ' besteld',
					timeout: 5000,
					actionHandler: undo,
					actionText: 'Undo'
				};
				snackbar.MaterialSnackbar.showSnackbar(data);
				document.querySelector("#refreshbar").style.display = ""
			} else {
				snackbar.MaterialSnackbar.showSnackbar({message: 'FOUT bij bestellen voor ' + elem.naam})
			}
		}
    };

    httpRequest.send(params);
}

function undo(elem) {
	snackbarbutton = document.querySelector('#snackbarbutton')
	
	uid = snackbarbutton.dataset.uid
	drank = snackbarbutton.dataset.drank
	snackbarbutton.removeAttribute('data-drank', undefined);
	snackbarbutton.removeAttribute('data-uid', undefined);

	document.querySelector('#progressbar').style.bottom = '0px';
	httpRequest = new XMLHttpRequest();

    httpRequest.open('POST', 'php/submit.php');
	params = 'what=undo&uid=' + uid + '&drank=' + drank
	httpRequest.setRequestHeader('Content-type','application/x-www-form-urlencoded');

    httpRequest.onreadystatechange = function() {
		if (httpRequest.readyState === XMLHttpRequest.DONE) {
			if (httpRequest.status === 200) {
				document.querySelector('#progressbar').style.bottom = '-10px';
				elem = document.querySelector('a[data-uid="' + uid + '"][data-drank="' + drank + '"]')
				elem.setAttribute('data-badge', httpRequest.responseText);
			} else {
				document.querySelector('#snackbar').MaterialSnackbar.showSnackbar({message: 'FOUT bij undo'})
			}
		}
    };
    
    httpRequest.send(params);
	
}

function filter(elem) {
	kaarten = document.querySelectorAll('.jaarkaart')
	for (var i = kaarten.length - 1; i >= 0; i--) {
		kaarten[i].style.display = 'none'
	}

	trs = document.querySelectorAll('#bestellen div.row')
	for (var i = 0; i < trs.length; i++) {
		tr = trs[i]

		// debugger
		if (tr.dataset.naam.indexOf(elem.value.toUpperCase()) === -1) {
			tr.style.maxHeight = "0px";
		} else {
			tr.style.maxHeight = "60px";

			// debugger;
			kaart = tr.parentElement.parentElement
			kaart.style.display = ''
		}
	}
}

function capitalizeFirstLetter(string) {
    return string.charAt(0).toUpperCase() + string.slice(1);
}

document.addEventListener('DOMContentLoaded', function() {
	document.querySelector('.mdl-layout').addEventListener('mdl-componentupgraded', function(e) {
        if (e.target.classList.contains('mdl-layout')) {
            items = document.querySelectorAll('i.user_avatar')
			for (var i = items.length - 1; i >= 0; i--) {
				items[i].style.background = "url(" + items[i].dataset.image + ")"
			}

			// if ran from homescreen
			if ((window.location.search.indexOf('utm_source') !== -1) || (window.navigator.standalone)) {
				document.querySelector('#refreshbutton').style.visibility = "visible"
				if ((/iphone|ipod|ipad/gi).test(navigator.platform) ) {
					document.body.style.paddingTop = "20px"
					document.querySelector('#yellowbar').style.height = "20px"
				}
			}
        }
    });
});


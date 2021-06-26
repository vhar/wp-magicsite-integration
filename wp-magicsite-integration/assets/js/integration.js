var $ = jQuery.noConflict();
$( document ).ready( function () {
	$( '#magicsite_content a' ).each( function () {
		var re = /^\//;
		var hr = this.getAttribute( 'href' );
		if ( re.test(hr) == true ) {
			$( this ).attr( 'href', magicsite_url + hr );
		}
	})

	$( '#magicsite_content img' ).each( function () {
		var re = /^\//;
		var hr = this.getAttribute( 'src' );
		if ( re.test(hr) == true ) {
			$( this ).attr( 'src', magicsite_url + hr );
		}
	})
});

$( document ).on( 'click', '.check-file-sign', function () {
	var url = magicsite_url + $( this ).data( 'url' );
	var nm = $( this ).data( 'name' );
	$.ajax( {
		type: 'POST',
		url: 'https://sign.edusite.ru/vsignf.php',
		data: { url: url },
		datatype: 'json',
		success: function ( response ) {
			if ( response.status == 1 ) {
				var msg = '';
				if ( response.mess !== undefined ) {
					msg += '<div class="error">'  + response.mess + '</div>';
				}
			} else if ( response.data !== undefined ) {
				var data = response.data,
				msg = '';
				if ( data.verify == 1 ) {
					msg += '<div class="popup"><div class="popup-title">Подписи</div><div class="signinfo popup-name">' + nm +'</div><div class="signinfo popup-link">' + url + '</div>';
					if ( data.signers !== undefined ) {
						$.each( data.signers, function ( i, e ) {
							dolgn = "";
							if ( e.cert.subjectName.T && e.cert.subjectName.T != "" ) {
								dolgn = ' (' + e.cert.subjectName.T + ')';
							}
							msg += '<div class="sign" ng-repeat="item in data.cryptoSigns" ng-if="data.cryptoSigns.length"> \
												<div class="signinfo signingTime">' + e.signingTime + '</div> \
												<div class="signinfo subjectNameSn">' + e.cert.subjectName.SN + ' ' + e.cert.subjectName.G + dolgn + '</div> \
												<div class="signinfo subjectNameO">' + e.cert.subjectName.O + '</div> \
												<div class="signinfo subjectNameCN">' + e.cert.subjectName.CN + '</div> \
											</div>';
						});
						msg += '<div class="signinfo subjectSign"><div>' + data.sign + '</div></div>';
					}
					msg += '</div>';
				}
			}
			$.fancybox.open( msg );
		},
		error: function ( xhr, ajaxOptions, thrownError ) {
			console.log( xhr.status );
			console.log( thrownError );
		}
	});
});

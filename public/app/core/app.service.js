import { v4 as uuidv4 } from 'uuid';
var pdfjsLib = window['pdfjs-dist/build/pdf'];
pdfjsLib.GlobalWorkerOptions.workerSrc = '/public/template/vendors/pdfjs/pdf.worker.js';

angular.module('siaApp').service('appService', appServiceFunction);
appServiceFunction.$inject = ['$http', '$q', '$compile'];

function appServiceFunction(http, q, compile)
{
    const sm = this;

    sm.httpCall = httpCall;
	sm.showModal = showModal;
	sm.renderPDF = renderPDF;
	sm.showCustomModal = showCustomModal;
	sm.showModalOne2Many = showModalOne2Many;
	sm.showModalWithData = showModalWithData;

    function httpCall($url, $method="GET", $data=false, $otherOption=false)
    {
        var deferred = q.defer();
		var httpConfig = {
			method: $method,
			url: $url
		};
		if($data) httpConfig['data'] = $data;
		if($otherOption) Object.assign(httpConfig, $otherOption);
		http(httpConfig).then((data) => deferred.resolve(data), (data) => {
			// this.showModal(data.data.message);
			alert(data.data.exception[0].message);
			deferred.reject('There was an error');
		});
	  
	  	return deferred.promise;
    }

	function showModal(scope, data)
	{
		let uid = uuidv4();
		scope[uid] = data;
		let el = compile(`<modal-dialog uid="${uid}" ></<modal-dialog>`)(scope);
	}
	
	function showCustomModal(scope, target, element)
	{
		let uid = uuidv4();
		let el = compile(`<custom-modal-dialog uid="${uid}" target="${target}" my-data="myData" ></<custom-modal-dialog>`)(scope);
		$(element).parent().append(el);
	}
	
	function showModalOne2Many(scope, target, element)
	{
		let uid = uuidv4();
		let targetName = target.split('/').pop();
		let element2Compile = `<modal-one2-many uid="${uid}" target="${target}" ></<modal-one2-many>`;

		if (targetName == 'pengajuan_krs') {
			element2Compile = `<modal-one2-many uid="${uid}" target="${target}" autoload="mata_kuliah" ></<modal-one2-many>`;
		}

		let el = compile(element2Compile)(scope);
		$(element).parent().append(el);
	}

	function showModalWithData(scope, target, element, attrs, additionalData)
	{
		let uid = uuidv4();
		scope[uid] = additionalData;
		let element2Compile = `<modal-with-data uid="${uid}" target="${target}" autoload="${attrs.btnShowData}" table="${attrs.table}" ></<modal-with-data>`;
		let el = compile(element2Compile)(scope);
		$(element).parent().append(el);
	}

	function renderPDF(base64)
	{
		let container = document.createElement( "div" );
		let binary_data = convertDataURIToBinary(base64);
		let loadingTask = pdfjsLib.getDocument({data: binary_data});
		let renderPage = [];

		container.classList.add("text-center");

		return loadingTask.promise.then(function(pdf) {
			var totalPages = pdf.numPages;
			// pdf.getPage( currentPage ).then( handlePages );

			for (let index = 1; index <= totalPages; index++) {
				let render = pdf.getPage( index ).then( (page) => {
					let divider = false;
					if (index != totalPages ) {
						divider = true;
					}
					handlePages(page, divider);
				});
				renderPage.push(render);
			}

			return Promise.all(renderPage).then(() => {
				return container;
			});

			function handlePages(page, divider) {
				var viewport = page.getViewport({scale: 1});
				var canvas = document.createElement( "canvas" );
				var context = canvas.getContext('2d');
				canvas.height = viewport.height;
				canvas.width = viewport.width;
				
				container.append(canvas);
				if (divider) {
					let hr = document.createElement('hr');
					container.append(hr);
				}

				let renderTask =  page.render({canvasContext: context, viewport: viewport});
				return renderTask.promise.then(function () {
					return true;
				});
			}
		});
	}

	function convertDataURIToBinary(base64) {
		var raw = window.atob(base64);
		var rawLength = raw.length;
		var array = new Uint8Array(new ArrayBuffer(rawLength));
	  
		for(var i = 0; i < rawLength; i++) {
		  array[i] = raw.charCodeAt(i);
		}
		return array;
	}

}

(function() {
  'use strict';

  angular
    .module('app.core')
    .factory('dataservice', dataservice);

  dataservice.$inject = ['$http', '$q', 'exception', 'logger'];
  /* @ngInject */
  function dataservice($http, $q, exception, logger) {
    var service = {
      getData: getData,
      getDataDetail: getDataDetail,
      getOption: getOption,
      getKartuPeserta: getKartuPeserta,
      postData: postData,
      getPdf: getPdf
    };

    return service;

    function getData(table, page=1) {
      return $http.get(`${table}/get?page=${page}`)
        .then(success)
        .catch(fail);
    }

    function getDataDetail(table, id) {
      return $http.get(`${table}/get/${id}`)
        .then(success)
        .catch(fail);
    }

    function getOption(table) {
      return $http.get(`${table}/selection`)
        .then(success)
        .catch(fail);
    }

    function getKartuPeserta(id) {
      return $http.get(`report/kartu_peserta/${id}`)
        .then(success)
        .catch(fail);
    }

    function postData(table, data, id='') {
      let action = id ? 'update' : 'add';
      let add_id = id ? `/${id}` : '';
      return $http.post(`${table}/${action}${add_id}`, data)
        .then(success)
        .catch(fail);
    }

    function success(response) {
      return response.data;
    }
    function fail(e) {
      return exception.catcher('XHR Failed for get data')(e);
    }

    function getPdf(base64) {
      let container = document.createElement( "div" );
      let binary_data = convertDataURIToBinary(base64);
      let loadingTask = pdfjsLib.getDocument({data: binary_data});
      let renderPage = [];

      container.classList.add("text-center");

      return loadingTask.promise.then(function(pdf) {
        var totalPages = pdf.numPages;

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
})();

(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('widgetCetakKhs', widgetCetakKhs);

  widgetCetakKhs.$inject = ['$state', 'dataservice', '$compile'];

  /* @ngInject */
  function widgetCetakKhs(state, dataservice, compile) {
    var directive = {
      restrict: 'EA',
      link: link
    };
    return directive;

    function link(scope, element, attr) {
      element.on('click', function(){
        let vm = scope.vm;
        let url = `khs/${vm.data.id}`;
        dataservice.getReport(url).then(function(response) {
          let base64 = response.content;
          scope.fileName = 'KHS.pdf';
          scope.type = 'pdf';
          scope.filetype = 'application/pdf';
          scope.base64 = base64;

          let preview_modal = '<modal-preview file="file" name="fileName" mimetype="filetype" base64="base64" type="type"></modal-preview>';
          let el = compile(preview_modal)(scope);

          dataservice.getPdf(base64).then(function(file){
            el.find('#pdf-container').append(file);
          });
        });
      });
    }
  }
})();

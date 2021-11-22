(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('widgetCetakKrs', widgetCetakKrs);

  widgetCetakKrs.$inject = ['$state', 'dataservice', '$compile'];

  /* @ngInject */
  function widgetCetakKrs(state, dataservice, compile) {
    var directive = {
      restrict: 'EA',
      link: link
    };
    return directive;

    function link(scope, element, attr) {
      element.on('click', function(){
        let vm = scope.vm;
        let url = `krs/${vm.data.id}`;
        dataservice.getReport(url).then(function(response) {
          let base64 = response.content;
          scope.fileName = 'KRS.pdf';
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

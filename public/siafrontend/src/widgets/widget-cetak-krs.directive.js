(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('widgetCetakKrs', widgetCetakKrs);

  widgetCetakKrs.$inject = ['$state', 'dataservice', '$compile', 'logger'];

  /* @ngInject */
  function widgetCetakKrs(state, dataservice, compile, logger) {
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
          
          if (base64) {
            scope.fileName = 'KRS.pdf';
            scope.type = 'pdf';
            scope.filetype = 'application/pdf';
            scope.base64 = base64;

            let preview_modal = '<modal-preview file="file" name="fileName" mimetype="filetype" base64="base64" type="type"></modal-preview>';
            let el = compile(preview_modal)(scope);
          } else {
            logger.error(response.error);
          }
        });
      });
    }
  }
})();

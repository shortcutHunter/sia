(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('widgetCetakTranskrip', widgetCetakTranskrip);

  widgetCetakTranskrip.$inject = ['$state', 'dataservice', '$compile'];

  /* @ngInject */
  function widgetCetakTranskrip(state, dataservice, compile) {
    var directive = {
      restrict: 'EA',
      link: link
    };
    return directive;

    function link(scope, element, attr) {
      element.on('click', function(){
        let vm = scope.$parent.vm;
        dataservice.getTranskripNilai(vm.data.id).then(function(response) {
          let base64 = response.content;
          scope.fileName = 'Transkrip Nilai.pdf';
          scope.type = 'pdf';
          scope.filetype = 'application/pdf';
          scope.base64 = base64;

          let preview_modal = '<modal-preview file="file" name="fileName" mimetype="filetype" base64="base64" type="type"></modal-preview>';
          let el = compile(preview_modal)(scope);
        });
      });
    }
  }
})();

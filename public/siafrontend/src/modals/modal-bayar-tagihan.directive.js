(function() {
  'use strict';

  angular
    .module('app.widgets')
    .directive('modalBayarTagihan', modalBayarTagihan);

  modalBayarTagihan.$inject = ['$state', 'dataservice'];

  /* @ngInject */
  function modalBayarTagihan(state, dataservice) {
    var directive = {
      restrict: 'EA',
      link: link,
      templateUrl: '/template/modal/bayar_tagihan',
      scope: {
        data: '=',
      },
      replace: true
    };
    return directive;

    function link(scope, element, attr) {
      let dm = scope.$parent.vm;
      scope.bukti_bayar = [];
      activate();

      scope.upload = upload;

      scope.$watch('vm.data.file', function(newVal, oldVal){
        if (newVal) {
          scope.data.tagihan_bukti_bayar.push({'file': angular.copy(newVal)});
          scope.vm.data.file = false;
        }
      });

      function activate() {
        $(element).modal('show');

        $(element).on('hidden.bs.modal', () => {
            element.remove();
        });
      }

      function upload() {
        let data = {
          tagihan_bukti_bayar: scope.data.tagihan_bukti_bayar
        };

        dataservice.postData('tagihan', data, scope.data.id).then(function(data){
          $(element).modal('hide');
          state.reload();
        });
      }
    }
  }
})();

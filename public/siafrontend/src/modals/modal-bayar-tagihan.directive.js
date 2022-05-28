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
      activate();

      scope.upload = upload;

      function activate() {
        $(element).modal('show');

        $(element).on('hidden.bs.modal', () => {
            element.remove();
        });
      }

      function upload() {
        let data = {
          transaksi: {
            nominal: scope.vm.data.nominal,
            tagihan_bukti_bayar: [{'file': scope.vm.data.file}]
          }
        };

        dataservice.postData('tagihan', data, scope.data.id).then(function(data){
          $(element).modal('hide');
          state.reload();
        });
      }
    }
  }
})();

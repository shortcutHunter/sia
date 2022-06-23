(function() {
  'use strict';

  angular
    .module('app.pembiayaan_lainnya')
    .controller('PembiayaanLainnyaFormController', PembiayaanLainnyaFormController);

  PembiayaanLainnyaFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function PembiayaanLainnyaFormController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Form Pembiayaan Lainnya';
    vm.table = 'pembiayaan_lainnya';

    activate();

    function activate() {
      if (stateParams.dataId) {
        var promises = [getDataDetail()];
        return $q.all(promises).then(function() {
          
        });
      }
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
      });
    }
  }
})();

(function() {
  'use strict';

  angular
    .module('app.pembiayaan_lainnya')
    .controller('PembiayaanLainnyaDetailController', PembiayaanLainnyaDetailController);

  PembiayaanLainnyaDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function PembiayaanLainnyaDetailController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Detail Pembiayaan Lainnya';
    vm.table = 'pembiayaan_lainnya';
    vm.data = {};
    vm.getDataDetail = getDataDetail;

    activate();

    function activate() {
      var promises = [getDataDetail()];
      return $q.all(promises).then(function() {
        
      });
    }

    function getDataDetail() {
      return dataservice.getDataDetail(vm.table, stateParams.dataId).then(function(response) {
        vm.data = response.data;
      });
    }
  }
})();

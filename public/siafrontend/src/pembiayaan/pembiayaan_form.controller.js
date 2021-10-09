(function() {
  'use strict';

  angular
    .module('app.pembiayaan')
    .controller('PembiayaanFormController', PembiayaanFormController);

  PembiayaanFormController.$inject = ['$q', 'dataservice', 'logger', '$stateParams'];
  /* @ngInject */
  function PembiayaanFormController($q, dataservice, logger, stateParams) {
    var vm = this;
    vm.title = 'Form Pembiayaan';
    vm.table = 'item';

    activate();

    function activate() {
      if (stateParams.dataId) {
        var promises = [getDataDetail()];
        return $q.all(promises).then(function() {
          logger.info('Data loaded');
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

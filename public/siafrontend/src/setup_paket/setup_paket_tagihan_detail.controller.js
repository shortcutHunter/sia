(function() {
  'use strict';

  angular
    .module('app.setup_paket')
    .controller('SetupPaketTagihanDetailController', SetupPaketTagihanDetailController);

  SetupPaketTagihanDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$compile', '$scope'];
  /* @ngInject */
  function SetupPaketTagihanDetailController($q, dataservice, logger, stateParams, compile, scope) {
    var vm = this;
    vm.title = 'Detail Tagihan';
    vm.data = {};

    activate();

    function activate() {
      var promises = [getDataDetail()];
      return $q.all(promises).then(function() {
        
      });
    }

    function getDataDetail() {
      return dataservice.getUrl(`/tagihan/mahasiswa/setup_paket/${stateParams.dataId}`).then(function(response) {
        vm.data = response.data;
      });
    }
  }
})();

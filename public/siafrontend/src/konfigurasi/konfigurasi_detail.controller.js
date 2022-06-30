(function() {
  'use strict';

  angular
    .module('app.konfigurasi')
    .controller('KonfigurasiDetailController', KonfigurasiDetailController);

  KonfigurasiDetailController.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$compile', '$scope'];
  /* @ngInject */
  function KonfigurasiDetailController($q, dataservice, logger, stateParams, compile, scope) {
    var vm = this;
    vm.title = 'Konfigurasi Sistem';
    vm.data = {};

    activate();

    function activate() {
      var promises = [getDataDetail()];
      return $q.all(promises).then(function() {
        
      });
    }

    function getDataDetail() {
      return dataservice.getUrl('/konfigurasi').then(function(response) {
        vm.data = response.data;
      });
    }
  }
})();

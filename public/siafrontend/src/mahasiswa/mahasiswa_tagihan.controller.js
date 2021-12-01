(function() {
  'use strict';

  angular
    .module('app.mahasiswa')
    .controller('MahasiswaTagihanController', MahasiswaTagihanController);

  MahasiswaTagihanController.$inject = ['$q', 'dataservice', 'logger', '$scope', '$stateParams'];
  /* @ngInject */
  function MahasiswaTagihanController($q, dataservice, logger, scope, stateParams) {
    var vm = this;
    vm.title = 'Tagihan Mahasiswa';
    vm.data = [];

    activate();

    function activate() {
      getData();
    }

    function getData() {
      return dataservice.getUrl('/mahasiswa/tagihan').then(function(response) {
        vm.data = response;
        logger.info('Data loaded');
      });
    }

  }
})();

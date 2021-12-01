(function() {
  'use strict';

  angular
    .module('app.mahasiswa')
    .controller('MahasiswaTagihanController', MahasiswaTagihanController);

  MahasiswaTagihanController.$inject = ['$q', 'dataservice', 'logger', '$scope', '$stateParams'];
  /* @ngInject */
  function MahasiswaTagihanController($q, dataservice, logger, scope, stateParams) {
    var vm = this;
    vm.title = 'KRS Mahasiswa';
    vm.data = [];

    activate();

    function activate() {
      getData();
    }

    function getData() {
      return dataservice.getUrl('/mahasiswa/tagihan').then(function(response) {
        vm.data = response.data;
        logger.info('Data loaded');
      });
    }

  }
})();

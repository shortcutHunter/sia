(function() {
  'use strict';

  angular
    .module('app.mahasiswa_baru')
    .controller('MahasiswaBaruDetail', MahasiswaBaruDetail);

  MahasiswaBaruDetail.$inject = ['$q', 'dataservice', 'logger', '$stateParams', '$compile', '$element', '$scope'];
  /* @ngInject */
  function MahasiswaBaruDetail($q, dataservice, logger, stateParams, compile, element, scope) {
    var vm = this;
    vm.title = 'Detail Data';
    vm.data = {};

    activate();

    function activate() {
      var promises = [getDataDetail()];
      return $q.all(promises).then(function() {
        
      });
    }

    function getDataDetail() {
      return dataservice.getUrl('/pmb/baru').then(function(response) {
        vm.data = response.data;
      });
    }

  }
})();

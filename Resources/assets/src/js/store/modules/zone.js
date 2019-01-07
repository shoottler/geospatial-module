const state = { config: {} }

function initialState () {
  return { config: {} }
}

const mutations = {
  set (state, { key, value }) {
    _.set(state, key, value)
  },
  reset (state) {
    const s = initialState()
    Object.keys(s).forEach((key) => {
      state[key] = s[key]
    })
  },
}
const actions   = {
  index ({ dispatch, rootState }) {
    return new Promise((resolve, reject) => {
      dispatch('asyncCall', {
        method   : 'get',
        url      : '/zones/',
        params   : { company_id: rootState.company.activeCompany.id },
        canCommit: false,
      }, { root: true }).then((response) => {
        resolve(response)
      }).catch((err) => {
        reject(err)
      })
    })
  },
  show ({ dispatch }, id) {
    return new Promise((resolve, reject) => {
      dispatch('asyncCall', {
        method   : 'get',
        url      : `/zones/${id}`,
        canCommit: false,
      }, { root: true }).then((response) => {
        resolve(response)
      }).catch((err) => {
        reject(err)
      })
    })
  },
  store ({ dispatch }, data) {
    return new Promise((resolve, reject) => {
      dispatch('asyncCall', {
        method   : 'post',
        url      : '/zones/create',
        data     : data,
        canCommit: false,
      }, { root: true }).then((response) => {
        resolve(response)
      }).catch((err) => {
        reject(err)
      })
    })
  },
  update ({ dispatch }, { id, zone }) {
    return new Promise((resolve, reject) => {
      dispatch('asyncCall', {
        method   : 'put',
        url      : `/zones/${id}/update`,
        data     : zone,
        canCommit: false,
      }, { root: true }).then((response) => {
        resolve(response)
      }).catch((err) => {
        reject(err)
      })
    })
  },
  destroy ({ dispatch }, id) {
    return new Promise((resolve, reject) => {
      dispatch('asyncCall', {
        method   : 'delete',
        url      : `/zones/${id}`,
        canCommit: false,
      }, { root: true }).then((response) => {
        resolve(response)
      }).catch((err) => {
        reject(err)
      })
    })
  },
}
export default {
  namespaced: true,
  state,
  mutations,
  actions,
}

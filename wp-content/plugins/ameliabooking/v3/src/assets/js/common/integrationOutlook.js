import httpClient from "../../../plugins/axios";
import {useRemoveUrlParameter, useUrlQueryParams} from "./helper";
import {useAuthorizationHeaderObject} from "../public/panel";

function useOutlookSync (code, successCallback) {
  let redirectURL = useRemoveUrlParameter(
    useRemoveUrlParameter(
      useRemoveUrlParameter(
        window.location.href,
        'code'
      ),
      'state'
    ),
    'type'
  )

  httpClient.post(
    '/outlook/authorization/token',
    {
      authCode: code,
      userId: useUrlQueryParams(window.location.href)['state'].split('amelia-outlook-calendar-auth-')[1],
      redirectUri: window.location.href.split('?')[0]
    }
  ).then(() => {
    history.pushState({}, null, redirectURL)
  }).catch((error) => {
    console.log(error)
  }).finally(() => {
    successCallback()
  })
}

function useOutlookConnect (store) {
  const amSettings = store.getters['getSettings']
  if (!store.getters['auth/getOutlookLoading']) {
    store.commit('auth/setOutlookLoading', true)

    if (!amSettings.outlookCalendar.accessToken) {
      httpClient
        .get(
          '/outlook/authorization/url/' + store.getters['employee/getId'],
          Object.assign(
            {
              redirectUri: window.location.href.split('?')[0],
            },
            useAuthorizationHeaderObject(store)
          )
        )
        .then((response) => {
          window.location.href = response.data.data.authUrl.replace(
            /redirect_uri=.+?&/,
            'redirect_uri=' + window.location.href + '&'
          )
        })
        .catch((error) => {
          console.log(error)

          store.commit('auth/setOutlookLoading', false)
        })
    }

    if (amSettings.outlookCalendar.accessToken) {
      httpClient
        .get(
          '/outlook-calendar/authorization/url/' +
            store.getters['employee/getId'],
          {
            params: {
              redirectUri: window.location.href,
            },
          }
        )
        .then((response) => {
          window.location.href = response.data.data.authUrl
        })
        .catch((error) => {
          console.log(error)

          store.commit('auth/setOutlookLoading', false)
        })
    }
  }
}

function useOutlookDisconnect (store, accountId = null) {
  store.commit('auth/setOutlookLoading', true)

  const endpoint = '/outlook/disconnect/' + store.getters['employee/getId']
  const data = accountId ? { accountId: accountId } : {}

  httpClient.post(endpoint, data).then((response) => {
    if (accountId) {
      const accounts = store.getters['employee/getOutlookCalendarAccounts'] || []

      const disconnectedAccount = accounts.find(a => String(a.id) === String(accountId))

      const updatedAccounts = accounts.filter(account => String(account.id) !== String(accountId))
      store.commit('employee/setOutlookCalendarAccounts', updatedAccounts)

      if (updatedAccounts.length === 0) {
        store.commit('employee/setOutlookId', '')
        store.commit('employee/setOutlookCalendarId', '')
        store.commit('employee/setOutlookToken', null)
        store.commit('employee/setOutlookCalendarBlockedCalendars', [])
        store.commit('auth/setOutlookCalendars', [])
      } else {
        const remainingCalendarIds = new Set()
        updatedAccounts.forEach(account => {
          if (account.calendarList && account.calendarList.length) {
            account.calendarList.forEach(cal => remainingCalendarIds.add(String(cal.id)))
          }
          if (account.calendarId) {
            remainingCalendarIds.add(String(account.calendarId))
          }
        })

        const removedCalendarIds = new Set()
        if (disconnectedAccount) {
          if (disconnectedAccount.calendarId) {
            removedCalendarIds.add(String(disconnectedAccount.calendarId))
          }
          if (disconnectedAccount.calendarList && disconnectedAccount.calendarList.length) {
            disconnectedAccount.calendarList.forEach(cal => removedCalendarIds.add(String(cal.id)))
          }
          if (disconnectedAccount.blockedCalendars && disconnectedAccount.blockedCalendars.length) {
            disconnectedAccount.blockedCalendars.forEach(id => removedCalendarIds.add(String(id)))
          }
        }
        if (response && response.data && response.data.data && response.data.data.removedCalendarIds) {
          response.data.data.removedCalendarIds.forEach(id => removedCalendarIds.add(String(id)))
        }

        const currentCalendarId = store.getters['employee/getOutlookCalendarId']
        if (currentCalendarId && (removedCalendarIds.has(String(currentCalendarId)) || !remainingCalendarIds.has(String(currentCalendarId)))) {
          store.commit('employee/setOutlookCalendarId', '')
        }

        const blocked = store.getters['employee/getOutlookCalendarBlockedCalendars'] || []
        const cleanedBlocked = blocked.filter(id => remainingCalendarIds.has(String(id)))
        store.commit('employee/setOutlookCalendarBlockedCalendars', cleanedBlocked)

        const firstRemainingConnected = updatedAccounts.find(a => !!a.token)
        if (firstRemainingConnected) {
          store.commit('employee/setOutlookId', firstRemainingConnected.id)
          store.commit('employee/setOutlookToken', firstRemainingConnected.token)
        }
      }
    } else {
      store.commit('employee/setOutlookId', '')
      store.commit('employee/setOutlookCalendarId', '')
      store.commit('employee/setOutlookToken', null)
      store.commit('employee/setOutlookCalendarAccounts', [])
      store.commit('employee/setOutlookCalendarBlockedCalendars', [])
      store.commit('auth/setOutlookCalendars', [])
    }
  }).catch((error) => {
    console.log(error)
  }).finally(() => {
    store.commit('auth/setOutlookLoading', false)
  })
}

export {
  useOutlookSync,
  useOutlookConnect,
  useOutlookDisconnect,
}

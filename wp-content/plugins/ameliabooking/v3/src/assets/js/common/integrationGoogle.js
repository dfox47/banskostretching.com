import httpClient from "../../../plugins/axios";
import {useRemoveUrlParameter, useUrlQueryParams} from "./helper";
import {useAuthorizationHeaderObject} from "../public/panel";

function useGoogleSync (code, successCallback) {
  let redirectURL = useRemoveUrlParameter(
    useRemoveUrlParameter(
      useRemoveUrlParameter(
        useRemoveUrlParameter(
          window.location.href,
          'code'
        ),
        'state'
      ),
      'scope'
    ),
    'iss'
  )

  httpClient.post(
    '/google/authorization/token',
    {
      authCode: code,
      userId: useUrlQueryParams(window.location.href)['state'],
      redirectUri: redirectURL
    }
  ).then(() => {
    history.pushState(null, null, redirectURL)
  }).catch((error) => {
    console.log(error)
  }).finally(() => {
    successCallback()
  })
}

function useGoogleConnect (store) {
  const amSettings = store.getters['getSettings']
  if (!store.getters['auth/getGoogleLoading']) {
    store.commit('auth/setGoogleLoading', true)

    let cleanUrl = useRemoveUrlParameter(window.location.href, 'iss')

    if (!amSettings.googleCalendar.accessToken) {
      httpClient.get(
          '/google/authorization/url/' + store.getters['employee/getId'],
          Object.assign(
              {
                'redirectUri': cleanUrl.split('?')[0],
              },
              useAuthorizationHeaderObject(store)
          )
      ).then((response) => {
        window.location.href = response.data.data.authUrl.replace(
            /redirect_uri=.+?&/,
            'redirect_uri=' + cleanUrl + '&'
        )
      }).catch((error) => {
        console.log(error)

        store.commit('auth/setGoogleLoading', false)
      })
    }

    if (amSettings.googleCalendar.accessToken) {
      httpClient.get(
          '/google-calendar/authorization/url/' + store.getters['employee/getId'],
          {
            params: {
              redirectUri: cleanUrl,
            }
          }
      ).then((response) => {
        window.location.href = response.data.data.authUrl
      }).catch((error) => {
        console.log(error)

        store.commit('auth/setGoogleLoading', false)
      })
    }
  }
}

async function useGoogleDisconnect (store, accountId = null) {
  store.commit('auth/setGoogleLoading', true)

  const endpoint = '/google/disconnect/' + store.getters['employee/getId']
  const data = accountId ? { accountId: accountId } : {}

  await httpClient.post(endpoint, data).then(async (response) => {
    if (accountId) {
      const accounts = store.getters['employee/getGoogleCalendarAccounts'] || []

      const disconnectedAccount = accounts.find(a => a.id === accountId)

      const updatedAccounts = accounts.filter(account => account.id !== accountId)
      store.commit('employee/setGoogleCalendarAccounts', updatedAccounts)

      if (updatedAccounts.length === 0) {
        store.commit('employee/setGoogleId', null)
        store.commit('employee/setGoogleCalendarId', '')
        store.commit('employee/setGoogleToken', null)
        store.commit('employee/setGoogleCalendarBlockedCalendars', [])
        store.commit('auth/setGoogleCalendars', [])
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

        const currentCalendarId = store.getters['employee/getGoogleCalendarId']
        if (currentCalendarId && !remainingCalendarIds.has(String(currentCalendarId))) {
          store.commit('employee/setGoogleCalendarId', '')
        }

        const blocked = store.getters['employee/getGoogleCalendarBlockedCalendars'] || []
        const cleanedBlocked = blocked.filter(id => remainingCalendarIds.has(String(id)))
        store.commit('employee/setGoogleCalendarBlockedCalendars', cleanedBlocked)
      }
    } else {
      store.commit('employee/setGoogleId', null)
      store.commit('employee/setGoogleCalendarId', '')
      store.commit('employee/setGoogleToken', null)
      store.commit('employee/setGoogleCalendarAccounts', [])
      store.commit('employee/setGoogleCalendarBlockedCalendars', [])
      store.commit('auth/setGoogleCalendars', [])
    }
  }).catch((error) => {
    console.log(error)
  }).finally(() => {
    store.commit('auth/setGoogleLoading', false)
  })
}

export {
  useGoogleSync,
  useGoogleConnect,
  useGoogleDisconnect,
}

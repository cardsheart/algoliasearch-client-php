import {
  PersonalizationApi,
  EchoRequester,
} from '@algolia/client-personalization';

const appId = process.env.ALGOLIA_APPLICATION_ID || 'test_app_id';
const apiKey = process.env.ALGOLIA_SEARCH_KEY || 'test_api_key';

const client = new PersonalizationApi(appId, apiKey, 'eu', {
  requester: new EchoRequester(),
});

describe('deleteUserProfile', () => {
  test('delete deleteUserProfile', async () => {
    const req = await client.deleteUserProfile({ userToken: 'UserToken' });
    expect(req).toMatchObject({
      path: '/1/profiles/UserToken',
      method: 'DELETE',
    });
  });
});

describe('getPersonalizationStrategy', () => {
  test('get getPersonalizationStrategy', async () => {
    const req = await client.getPersonalizationStrategy();
    expect(req).toMatchObject({
      path: '/1/strategies/personalization',
      method: 'GET',
    });
  });
});

describe('getUserTokenProfile', () => {
  test('get getUserTokenProfile', async () => {
    const req = await client.getUserTokenProfile({ userToken: 'UserToken' });
    expect(req).toMatchObject({
      path: '/1/profiles/personalization/UserToken',
      method: 'GET',
    });
  });
});

describe('setPersonalizationStrategy', () => {
  test('set setPersonalizationStrategy', async () => {
    const req = await client.setPersonalizationStrategy({
      personalizationStrategyObject: {
        eventScoring: [{ score: 42, eventName: 'Algolia', eventType: 'Event' }],
        facetScoring: [{ score: 42, facetName: 'Event' }],
        personalizationImpact: 42,
      },
    });
    expect(req).toMatchObject({
      path: '/1/strategies/personalization',
      method: 'POST',
      data: {
        eventScoring: [{ score: 42, eventName: 'Algolia', eventType: 'Event' }],
        facetScoring: [{ score: 42, facetName: 'Event' }],
        personalizationImpact: 42,
      },
    });
  });
});
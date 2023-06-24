CREATE TABLE providers
(
    id          SERIAL PRIMARY KEY,
    name        VARCHAR(255) NOT NULL,
    description TEXT         NOT NULL,
    url         VARCHAR(255) NOT NULL,
    urn         VARCHAR(255) NOT NULL,
    created_at  TIMESTAMP    NOT NULL,
    UNIQUE (urn)
);

CREATE TABLE sources
(
    id                   SERIAL PRIMARY KEY,
    name                 VARCHAR(255) NOT NULL,
    description          TEXT         NOT NULL,
    min_crawl_interval   INTEGER      NOT NULL,
    max_crawl_interval   INTEGER      NOT NULL,
    min_recrawl_interval INTEGER      NOT NULL,
    max_recrawl_interval INTEGER      NOT NULL,

    provider_id          INTEGER      NOT NULL REFERENCES providers (id) ON DELETE CASCADE,
    urn                  VARCHAR(255) NOT NULL,
    created_at           TIMESTAMP    NOT NULL,
    UNIQUE (urn)
);

CREATE TABLE sub_sources
(
    id               SERIAL PRIMARY KEY,
    parent_source_id INTEGER NOT NULL REFERENCES sources (id) ON DELETE CASCADE,
    sub_source_id    INTEGER NOT NULL REFERENCES sources (id) ON DELETE CASCADE,
    UNIQUE (parent_source_id, sub_source_id)
);

CREATE TABLE crawls
(
    id           SERIAL PRIMARY KEY,
    source_id    INTEGER   NOT NULL,
    range_offset JSONB     NOT NULL,
    created_at   TIMESTAMP NOT NULL,
    UNIQUE (source_id)
);

INSERT INTO providers (id, name, description, url, urn, created_at)
VALUES (1, 'EA Forum',
        'The Effective Altruism Forum is a place for discussion of effective altruism and related topics.',
        'https://forum.effectivealtruism.org/',
        'urn:forevue:provider:eaforum:3484d3d:97fa66d1cc41bae08b5c88c7322b35b17cf5a1b2559205eb2aa3bd81b588d162',
        '2023-06-24 12:36:56.109962');

INSERT INTO "sources" ("id", "name", "description", "min_crawl_interval", "max_crawl_interval", "min_recrawl_interval",
                       "max_recrawl_interval", "provider_id", "urn", "created_at")
VALUES (1, 'EA Forum Posts', 'Posts from the EA Forum', 86400, 172800, 2592000, 5184000, 1,
        'urn:forevue:source:eaposts:3484d3d:4ea41849cb1ca466e20ceebc5f83a576d33abbf103df53b2a0adaf2dc69166f4',
        '2023-06-24 12:36:56.215515'),
       (2, 'EA Forum Comments', 'Comments from an EA Forum Post', 0, 0, 0, 0, 1,
        'urn:forevue:source:eacomments:3484d3d:ed40452f5d9b1383fdd9f332b825a97b22d3e6d7f72430669fc971f148b3bda7',
        '2023-06-24 12:36:56.32097'),
       (3, 'EA Wiki', 'The EA Wiki is a community-curated compendium of knowledge about effective altruism.', 0, 0, 0,
        0, 1, 'urn:forevue:source:eawiki:3484d3d:96a391448653d8b5a976ccc429d709640e8bfc4b8bfd81ab9eff02818fac83b0',
        '2023-06-24 12:37:56.774842');

INSERT INTO "sub_sources" ("id", "parent_source_id", "sub_source_id")
VALUES (1, 1, 2);

INSERT INTO "crawls" ("id", "source_id", "range_offset", "created_at")
VALUES (1, 1, '{
    "start": "2023-03-24 00:00:00",
    "offset": 1176
}', '2023-06-24 12:37:56.598437'),
       (2, 3, '{
           "offset": 998
       }', '2023-06-24 12:37:58.380873');


create table doctrine_migration_versions
(
    version        varchar(191) not null,
    executed_at    datetime     null,
    execution_time int          null,
    primary key (version)
)
    collate = utf8_unicode_ci;

create table messenger_messages
(
    id           bigint auto_increment
        primary key,
    body         longtext     not null,
    headers      longtext     not null,
    queue_name   varchar(190) not null,
    created_at   datetime     not null,
    available_at datetime     not null,
    delivered_at datetime     null
)
    collate = utf8mb4_unicode_ci;

create index IDX_75EA56E016BA31DB
    on messenger_messages (delivered_at);

create index IDX_75EA56E0E3BD61CE
    on messenger_messages (available_at);

create index IDX_75EA56E0FB7336F0
    on messenger_messages (queue_name);

create table tag
(
    id  int auto_increment
        primary key,
    mot varchar(255) not null
)
    collate = utf8mb4_unicode_ci;

create table user
(
    id               int auto_increment
        primary key,
    email            varchar(180) not null,
    roles            json         not null,
    password         varchar(255) not null,
    prenom           varchar(180) not null,
    nom              varchar(180) not null,
    photo            varchar(255) null,
    reset_token      varchar(255) null,
    token_expiration datetime     null,
    created_at       datetime     not null,
    updated_at       datetime     not null,
    constraint UNIQ_8D93D649E7927C74
        unique (email)
)
    collate = utf8mb4_unicode_ci;

create table article
(
    id        int auto_increment
        primary key,
    title     varchar(255) not null,
    content   longtext     not null,
    status    int          not null,
    author_id int          null,
    constraint FK_23A0E66F675F31B
        foreign key (author_id) references user (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_23A0E66F675F31B
    on article (author_id);

create table comment
(
    id            int auto_increment
        primary key,
    article_id_id int      not null,
    content       longtext not null,
    author_id     int      not null,
    constraint FK_9474526C8F3EC46
        foreign key (article_id_id) references article (id),
    constraint FK_9474526CF675F31B
        foreign key (author_id) references user (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_9474526C8F3EC46
    on comment (article_id_id);

create index IDX_9474526CF675F31B
    on comment (author_id);

create table role
(
    id       int auto_increment
        primary key,
    users_id int          null,
    label    varchar(255) not null,
    constraint FK_57698A6A67B3B43D
        foreign key (users_id) references user (id)
)
    collate = utf8mb4_unicode_ci;

create index IDX_57698A6A67B3B43D
    on role (users_id);

create table tag_article
(
    tag_id     int not null,
    article_id int not null,
    primary key (tag_id, article_id),
    constraint FK_300B23CC7294869C
        foreign key (article_id) references article (id)
            on delete cascade,
    constraint FK_300B23CCBAD26311
        foreign key (tag_id) references tag (id)
            on delete cascade
)
    collate = utf8mb4_unicode_ci;

create index IDX_300B23CC7294869C
    on tag_article (article_id);

create index IDX_300B23CCBAD26311
    on tag_article (tag_id);




INSERT INTO blog.article (id, title, content, status, author_id, image, created_at, updated_at, summary) VALUES (3, 'Bye-bye ChatGPT: AI Tools As Good As ChatGPT (But Few People Are Using Them)', 'Over the past months, ChatGPT has gained a lot of users because it’s so good at writing emails, blogs, code, and more. However, there are other tools that use the model behind ChatGPT to go beyond what ChatGPT can do.

In this article, I’ll share a list of tools that I believe are better than ChatGPT because they offer extra features, can be customized, and were built for specific use cases using GPT-3.5/GPT-4.', 0, 21, 'https://miro.medium.com/v2/resize:fit:1100/format:webp/1*pPDGinb8Z17nXMmifwIESw.png', '2023-05-04 12:47:01', '2023-05-04 12:47:02', 'ChatGPT has gained a lot of users because it’s so good at writing emails, blogs, code, and more. However, there are other tools that use the model behind ChatGPT to go beyond what ChatG PT can do. In this article, I’ll share a list of tools that I believe are better than chatGPT because they offer extra features and can be customized.');
INSERT INTO blog.article (id, title, content, status, author_id, image, created_at, updated_at, summary) VALUES (4, 'Web Developer Roadmap 2023: Beginner’s Guide', 'Web development is a rapidly evolving field. Definitely it requires constant learning and increasing knowledge about programming. Increasing use of the internet and the rise of startups and e-commerce platforms we are using websites more than ever. Having a website is essential for all businesses. The size of the company does not matter to have a website or not. These requirements increased the demand for web developers.', 0, 21, 'https://miro.medium.com/v2/resize:fit:1100/format:webp/1*joTScF2Mqv8Hl2gr6AIj7A.jpeg', '2023-05-04 12:47:04', '2023-05-04 12:47:05', 'Having a website is essential for all businesses. The size of the company does not matter to have a website or not. Increasing use of the internet and the rise of startups and e-commerce platforms we are using websites more than ever. These requirements increased the demand for web developers.');
INSERT INTO blog.article (id, title, content, status, author_id, image, created_at, updated_at, summary) VALUES (5, '2023: Laravel — The Game Changer for Web Development', 'As a 10-year experienced web developer, I can confidently say that Laravel is set to be the game changer for web development in 2023.

For those who may not be familiar, Laravel is a PHP framework that has been gaining popularity in recent years. It is known for its elegant syntax, easy-to-use tools, and robust features that make it a favorite among web developers.

', 0, 21, 'https://miro.medium.com/v2/resize:fit:1100/0*JR_byv_WTCxCgQ8s', '2023-05-04 12:47:07', '2023-05-04 12:47:08', 'As a 10-year experienced web developer, I can confidently say that Laravel is set to be the game changer for web development in 2023. It is known for its elegant syntax, easy-to-use tools, and robust features that make it a favorite among web developers.');
INSERT INTO blog.article (id, title, content, status, author_id, image, created_at, updated_at, summary) VALUES (7, 'The Density of What Matters in the Universe', '<h1 id="in-my-freshman-seminar">In my Freshman Seminar </h1>
<p>class at Harvard, the students were surprised to learn that the density of air is merely a thousand times lower than that of liquids or solids. This implies that atoms are separated roughly ten times farther apart in the air we breathe than in the water we drink, or the chair we sit on, or the Earth on which the chair rests. I noted: “Surely, ordinary matter in outer space is far more rarefied than even air.” I asked my students to guess by how much, andthen gave the answer.</p>
', 0, 24, '/uploads/47a94ac074bc055cd91843aa98ee53f4.jpg', '2023-05-04 12:47:12', '2023-05-04 12:47:17', 'The density of air is merely a thousand times lower than that of liquids or solids. This implies that atoms are separated roughly ten times farther apart in the air we breathe than in the water we drink, or the chair we sit on. I asked my students to guess by how much, andthen gave the answer.');
INSERT INTO blog.article (id, title, content, status, author_id, image, created_at, updated_at, summary) VALUES (8, 'What we’re reading: Declutter your ', 'There is a lot of advice out there about the best time to wake up, the best time to work out, and the best habits to develop. The advice is so abundant that, if you try to incorporate them all, your wires just might overload.

Sometimes you are the best judge of what works best for you. That’s what communication and connection coach May Pang discovered when she sampled — and rejected — several popular productivity methods. Her original habits were just fine, writes Pang, who says simplifying is really what helped her to declutter her brain.', 0, 24, '/uploads/efb3cd1c3eb7f3898b3f202e70fd6474.jpg', '2023-05-04 12:47:13', '2023-05-04 12:47:15', 'Communication and connection coach May Pang rejected popular productivity methods. Pang says simplifying is really what helped her to declutter her brain. She says the best way to get the most out of your life is to focus on the things that matter most to you, rather than what you think should happen next.');
INSERT INTO blog.article (id, title, content, status, author_id, image, created_at, updated_at, summary) VALUES (10, 'Starting A New Hobby: What My Research Shows', '<h1 id="disclaimer">Disclaimer:</h1>
<p> I haven’t done any formal research on this (or any) subject. You will find no evidence of systematic investigation or academic rigor here, other than that achieved by watching YouTube and Instagram for a whole lot of hours. Nevertheless, I’m super stoked to share with you what happened when my role at work got eliminated last fall and why each of us deserves to spend at least some of our time doing a thing we really enjoy.qwd</p>
', 0, 24, '/uploads/569fa2988b1c17db62c425f9972dc7b2.webp', '2023-05-05 13:22:32', '2023-05-05 13:22:32', 'This is the story of why I quit my job at work to spend more time doing what I love. The author has no formal research on the subject, other than to watch YouTube and Instagram for hours. He hopes to inspire others to do what they love with their spare time.');

INSERT INTO blog.role (id, label) VALUES (1, 'user');
INSERT INTO blog.role (id, label) VALUES (2, 'admin');

INSERT INTO blog.tag (id, mot) VALUES (4, 'Science');
INSERT INTO blog.tag (id, mot) VALUES (5, 'IT');
INSERT INTO blog.tag (id, mot) VALUES (6, 'Biology');

INSERT INTO blog.tag_article (tag_id, article_id) VALUES (5, 4);
INSERT INTO blog.tag_article (tag_id, article_id) VALUES (5, 5);
INSERT INTO blog.tag_article (tag_id, article_id) VALUES (5, 7);
INSERT INTO blog.tag_article (tag_id, article_id) VALUES (6, 3);
INSERT INTO blog.tag_article (tag_id, article_id) VALUES (6, 7);
INSERT INTO blog.tag_article (tag_id, article_id) VALUES (6, 8);
INSERT INTO blog.tag_article (tag_id, article_id) VALUES (6, 10);

INSERT INTO blog.user (id, email, password, prenom, nom, photo, reset_token, token_expiration, created_at, updated_at, role_id) VALUES (1, 'omarouafi12@gmail.co', '$2y$13$DEXCSc.r11/UeqWVhp5KYedc.N2NE12MJsxALG6WFbiEEdkTRRvgy', 'bio', 'fwefbl', null, null, null, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1);
INSERT INTO blog.user (id, email, password, prenom, nom, photo, reset_token, token_expiration, created_at, updated_at, role_id) VALUES (14, 'helo@sad.comkdrdss', '$2y$13$H.YHQkm3uFoBs5XTDpbyDeaI03own3FA8o8SgqlZMdFCeTLI.wnHK', '', '', null, null, null, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1);
INSERT INTO blog.user (id, email, password, prenom, nom, photo, reset_token, token_expiration, created_at, updated_at, role_id) VALUES (15, 'helos@sad.comkdrdss', '$2y$13$DrOu.TXfT1m3/Xu5gSx30u./AzthPbvKfzdrXV3rRe7FdfsGZ/TXm', '', '', null, null, null, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1);
INSERT INTO blog.user (id, email, password, prenom, nom, photo, reset_token, token_expiration, created_at, updated_at, role_id) VALUES (16, 'helos@sad.comkdrdsss', '$2y$13$57TMC4vfY29AEL/1Xqt0UuYQq7MklQUdCX0vmx.2XZPYJ/FwuGJ1C', '', '', null, null, null, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1);
INSERT INTO blog.user (id, email, password, prenom, nom, photo, reset_token, token_expiration, created_at, updated_at, role_id) VALUES (17, 'heloss@sad.comkdrdsss', '$2y$13$XC6rNkApuGl8hNagI6yLAemtGVTp9FsYulY2yJ4b4Y/bX1ZZQxSSm', '', '', null, null, null, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1);
INSERT INTO blog.user (id, email, password, prenom, nom, photo, reset_token, token_expiration, created_at, updated_at, role_id) VALUES (18, 'heloss@sad.comkdrdssss', '$2y$13$Kh4alNdZ8juklMxJEj9yeOnVDDqP0hRnDvdeZh0YNgSIuN5pRLfr.', '', '', null, null, null, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1);
INSERT INTO blog.user (id, email, password, prenom, nom, photo, reset_token, token_expiration, created_at, updated_at, role_id) VALUES (19, 'fert@sad.comkdrdssss', '$2y$13$BDOpaqIBwI6y15wOGqZBJ.MQXGbW6aEtQ6qYSPRbqX2GACQsyawM.', '', '', null, null, null, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1);
INSERT INTO blog.user (id, email, password, prenom, nom, photo, reset_token, token_expiration, created_at, updated_at, role_id) VALUES (20, 'omarouafi@gmail.com', '$2y$13$LoWsEINQzEFDvZIKkj4oB.BGxSm74mx9Neeh9jqLVlsfFjh0J1YhK', '', '', null, null, null, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1);
INSERT INTO blog.user (id, email, password, prenom, nom, photo, reset_token, token_expiration, created_at, updated_at, role_id) VALUES (21, 'doe@gmail.co', '$2y$13$Jk24wg4/XSgUxJpznBc/4Oc3JJlyPhydYjDjyxu4QMXIZ9IY/wxQC', 'Doe', 'Johnathan', 'https://miro.medium.com/v2/resize:fill:132:132/1*oTq5RWcSwzoDiuAO_OBhaw.jpeg', '1rg7BVvJRLBXFvFxf_-_Ke2aqYRx1KQy1L2JkPnutAE', '2023-04-26 15:02:14', '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1);
INSERT INTO blog.user (id, email, password, prenom, nom, photo, reset_token, token_expiration, created_at, updated_at, role_id) VALUES (22, 'george@gmail.com', '$2y$13$69SlySlwk9eZUI.k0BgeV.mN6NghKN.uEMh6rMasToUPqphPbXlPm', 'George', 'Emily', null, null, null, '0000-00-00 00:00:00', '0000-00-00 00:00:00', 1);
INSERT INTO blog.user (id, email, password, prenom, nom, photo, reset_token, token_expiration, created_at, updated_at, role_id) VALUES (23, 'ss@gmail.co', '$2y$13$iLW5bRBFi3PLscIISrY7rO2vliOT.aliFo/edMbogahBNKh3jcVfG', 'pinfwe', 'fwef', null, null, null, '2023-05-02 12:46:10', '2023-05-02 12:46:10', 1);
INSERT INTO blog.user (id, email, password, prenom, nom, photo, reset_token, token_expiration, created_at, updated_at, role_id) VALUES (24, 'dds@gmail.co', '$2y$13$iQw9VHjAaJO.k5ZFbgxgyuHzI5Lf2WlqauvL1RfQ46G13sgqVS.hi', 'Donald', 'McNeil', 'https://miro.medium.com/v2/resize:fill:132:132/1*w4xWkbxe1SagK3TQkLmM5A.jpeg', null, null, '2023-05-02 12:46:27', '2023-05-02 12:46:27', 1);
INSERT INTO blog.user (id, email, password, prenom, nom, photo, reset_token, token_expiration, created_at, updated_at, role_id) VALUES (25, 'dds@gmail.cod', '$2y$13$ytw0/Musg5V7cBRQL3EwJuPm8CbNw/RtB/7FoJ21smJhiUVztro/C', 'fwf', 'geirog', null, null, null, '2023-05-02 13:13:22', '2023-05-02 13:13:22', 2);
INSERT INTO blog.user (id, email, password, prenom, nom, photo, reset_token, token_expiration, created_at, updated_at, role_id) VALUES (26, 'fwepj@sa.cp', '$2y$13$DrGJdGNLLz.Lkoi41Vs.uOlw9/Orlw8KpzQ.fInoYQQOkDwAMYqOe', 'fwepon', 'ffwe', null, null, null, '2023-05-02 13:23:17', '2023-05-02 13:23:17', 2);


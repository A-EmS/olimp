DB tables:
project_stages,
project_parts

1)
in DB:
CREATE TABLE `project_stages` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) DEFAULT NULL,
  `stage` varchar(255) NOT NULL,
  `code` varchar(255) DEFAULT NULL,

  `create_user` int(11) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8;

CREATE TABLE `project_parts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `country_id` int(11) DEFAULT NULL,
  `project_stage_id` int(11) DEFAULT NULL,
  `part` varchar(255) DEFAULT NULL,
  `code` varchar(255) DEFAULT NULL,

  `create_user` int(11) DEFAULT NULL,
  `create_date` datetime DEFAULT NULL,
  `update_user` int(11) DEFAULT NULL,
  `update_date` datetime DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=1 DEFAULT CHARSET=utf8

2)
/****/
in:
web/vuejs/src/router/index.js

add:
{
    path: '/projectStages',
    name: 'projectStages',
    component: () => import('../views/modules/engineering/projectStages/ProjectStages.vue'),
},
{
    path: '/projectParts',
    name: 'projectParts',
    component: () => import('../views/modules/engineering/projectParts/ProjectParts.vue'),
},

3)
add files into:
web/vuejs/src/views/modules

4)
add to SideBar.vue under MODULE section

{
    title: this.$store.state.t('Engineering'),
    icon: 'pe-7s-plugin',
    child: [
        {
            href: '/projectStages',
            title: this.$store.state.t('Project Stages'),
        },
        {
            href: '/projectParts',
            title: this.$store.state.t('Project Parts'),
        },
    ]
},
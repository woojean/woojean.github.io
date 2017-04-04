# MySQL如何进行慢查询日志分析？

慢查询相关的配置参数：
log_slow_queries					# 是否打开慢查询日志，得先确保=ON后面才有得分析
long_query_time					    # 查询时间大于多少秒的SQL被当做是慢查询，一般设为1S
log_queries_not_using_indexes		# 是否将没有使用索引的记录写入慢查询日志
slow_query_log_file				    # 慢查询日志存放路径
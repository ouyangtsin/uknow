<?php
// +----------------------------------------------------------------------
// | UKnowing [You Know] 简称 UK
// +----------------------------------------------------------------------
// | Copyright (c) 2020-2021 https://www.uknowing.com
// +----------------------------------------------------------------------
// | UKnowing一款基于TP6开发的社交化知识付费问答系统、企业内部知识库系统，打造私有社交化问答、内部知识存储
// +----------------------------------------------------------------------
// | Author: UK团队 <devteam@uknowing.com>
// +----------------------------------------------------------------------
namespace app\common\paginator;
use think\Paginator;

/**
 * Bootstrap 分页驱动
 */
class UKnowing extends Paginator
{
	/**
	 * 上一页按钮
	 * @param string $text
	 * @return string
	 */
	protected function getPreviousButton(string $text = "&laquo;"): string {

		if ($this->currentPage() <= 1) {
			return $this->getDisabledTextWrapper($text);
		}

		$url = $this->url(
			$this->currentPage() - 1
		);

		return $this->getPageLinkWrapper($url, $text);
	}

	/**
	 * 下一页按钮
	 * @param string $text
	 * @return string
	 */
	protected function getNextButton(string $text = '&raquo;'): string {
		if (!$this->hasMore) {
			return $this->getDisabledTextWrapper($text);
		}

		$url = $this->url($this->currentPage() + 1);

		return $this->getPageLinkWrapper($url, $text);
	}

	/**
	 * 页码按钮
	 * @return string
	 */
	protected function getLinks(): string {
		if ($this->simple) {
			return '';
		}

		$block = [
			'first' => null,
			'slider' => null,
			'last' => null,
		];

		$side = 3;
		$window = $side * 2;

		if ($this->lastPage < $window + 6) {
			$block['first'] = $this->getUrlRange(1, $this->lastPage);
		} elseif ($this->currentPage <= $window) {
			$block['first'] = $this->getUrlRange(1, $window + 2);
			$block['last'] = $this->getUrlRange($this->lastPage - 1, $this->lastPage);
		} elseif ($this->currentPage > ($this->lastPage - $window)) {
			$block['first'] = $this->getUrlRange(1, 2);
			$block['last'] = $this->getUrlRange($this->lastPage - ($window + 2), $this->lastPage);
		} else {
			$block['first'] = $this->getUrlRange(1, 2);
			$block['slider'] = $this->getUrlRange($this->currentPage - $side, $this->currentPage + $side);
			$block['last'] = $this->getUrlRange($this->lastPage - 1, $this->lastPage);
		}

		$html = '';
		if (is_array($block['first'])) {
			$html .= $this->getUrlLinks($block['first']);
		}

		if (is_array($block['slider'])) {
			$html .= $this->getDots();
			$html .= $this->getUrlLinks($block['slider']);
		}

		if (is_array($block['last'])) {
			$html .= $this->getDots();
			$html .= $this->getUrlLinks($block['last']);
		}

		return $html;
	}

	/**
	 * 渲染分页html
	 * @return mixed
	 */
	public function render()
    {
		if ($this->hasPages()) {
			if ($this->simple) {
				return sprintf(
					'<div class="dataTables_paginate paging_simple_numbers" id="highlightRowColumn_paginate"><ul class="pagination pagination-sm uk-pagination">%s %s</ul></div>',
					$this->getPreviousButton(),
					$this->getNextButton()
				);
			} else {
				return sprintf(
					'<div class="dataTables_paginate paging_simple_numbers" id="highlightRowColumn_paginate"><ul class="pagination pagination-sm uk-pagination">%s %s %s</ul></div>',
					$this->getPreviousButton(),
					$this->getLinks(),
					$this->getNextButton()
				);
			}
		}
	}

	/**
	 * 生成一个可点击的按钮
	 *
	 * @param  string $url
	 * @param  string $page
	 * @return string
	 */
	protected function getAvailablePageWrapper(string $url, string $page): string {
	    $attrHtml = isset($this->options['pjax']) && $this->options['pjax'] ? 'data-pjax="'.$this->options['pjax'].'"' : '';
		return '<li class="paginate_button page-item"><a href="' . htmlentities($url) . '"  class="page-link" '.$attrHtml.'>' . $page . '</a></li>';
	}

	/**
	 * 生成一个禁用的按钮
	 *
	 * @param  string $text
	 * @return string
	 */
	protected function getDisabledTextWrapper(string $text): string {
		return '<li class="disabled uk-disabled"><span>' . $text . '</span></li>';
	}

	/**
	 * 生成一个激活的按钮
	 *
	 * @param  string $text
	 * @return string
	 */
	protected function getActivePageWrapper(string $text): string {
        $attrHtml = isset($this->options['pjax']) && $this->options['pjax'] ? 'data-pjax="'.$this->options['pjax'].'"' : '';
		return '<li class="paginate_button page-item active uk-active"><a class="page-link" '.$attrHtml.'>' . $text . '</a></li>';
	}

	/**
	 * 生成省略号按钮
	 *
	 * @return string
	 */
	protected function getDots(): string {
		return $this->getDisabledTextWrapper('...');
	}

	/**
	 * 批量生成页码按钮.
	 *
	 * @param  array $urls
	 * @return string
	 */
	protected function getUrlLinks(array $urls): string{
		$html = '';

		foreach ($urls as $page => $url) {
			$html .= $this->getPageLinkWrapper($url, $page);
		}

		return $html;
	}

	/**
	 * 生成普通页码按钮
	 *
	 * @param  string $url
	 * @param  string    $page
	 * @return string
	 */
	protected function getPageLinkWrapper(string $url, string $page): string {
		if ($this->currentPage() == (int)$page) {
			return $this->getActivePageWrapper($page);
		}

		return $this->getAvailablePageWrapper($url, $page);
	}
}

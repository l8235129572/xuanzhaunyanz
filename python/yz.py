import cv2
import numpy as np

img = cv2.imread('img/2/2.png')
rows,cols = img.shape[0:2]
# cols-1 and rows-1 are the coordinate limits.
M = cv2.getRotationMatrix2D(((cols-1)/2.0,(rows-1)/2.0),0,1)
dst = cv2.warpAffine(img,M,(cols,rows))
# 将图片高和宽分别赋值给x，y
x, y = dst.shape[0:2]
# 缩放到原来的二分之一，输出尺寸格式为（宽，高）
img_test1 = cv2.resize(dst, (8, 8))
#转换成灰度图
im_gray=cv2.cvtColor(img_test1,cv2.COLOR_BGR2GRAY)
#使用自适应阈值进行二值化处理，其他二值化方法可查询API使用
im_at_mean = cv2.adaptiveThreshold(im_gray, 255, cv2.ADAPTIVE_THRESH_GAUSSIAN_C , cv2.THRESH_BINARY, 5,10) #使用自适应阈值进行二值化处理，其他二值化方法可查询API使用
img4 = cv2.cvtColor(im_at_mean, cv2.COLOR_BGR2RGB) 
str1=''
for i in range(8):
    for o in range(8):
        r, g, b = img4[i][o]
        if(r==0):
            str1= str1 + str(0)
        else:
            str1= str1 + str(1)  
with open("test1.txt","a") as f:
    f.write(str1 +"\n")  #这句话自带文件关闭功能，不需要再写f.close()




#cv2.imshow('img',img)
#cv2.imshow('dst',im_at_mean)
#cv2.waitKey(0) 
